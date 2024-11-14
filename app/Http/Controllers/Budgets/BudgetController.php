<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use App\Mail\MailBudget;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Budgets\BudgetStatu;
use App\Models\Budgets\BudgetConceptSupplierRequest;
use App\Models\Budgets\BudgetCustomPDF;
use App\Models\Budgets\BudgetSend;
use App\Models\Services\ServiceCategories;
use App\Models\Tasks\Task;
use App\Models\Company\CompanyDetails;
use App\Models\Clients\Client;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\InvoiceReferenceAutoincrement;
use App\Models\Logs\LogsEmail;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Petitions\Petition;
use App\Models\Projects\Project;
use App\Models\Users\ClientUserOrder;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::all();
        return view('budgets.index', compact('budgets'));
    }
    public function indexUser()
    {
        $budgets = Budget::where('admin_user_id',auth()->user()->id)->get();
        return view('budgets.indexUser', compact('budgets'));
    }

    public function statusProjects()
    {
        $userId = Auth::user()->id;
        $usuario = User::find($userId);

        // Obtener los clientes ordenados por el usuario
        // $clientes = $usuario->orderedClients()
        //                     ->orderBy('order')
        //                     ->with('client')
        //                     ->get()
        //                     ->pluck('client');

        // // Si no hay un orden guardado, mostrar los clientes por defecto
        // if ($clientes->isEmpty()) {}
            $clientes = $usuario->clientes()->orderBy('name')->get();


        return view('budgets.status', compact('clientes'));
    }

    public function saveOrder(Request $request)
    {
        $userId = Auth::user()->id;
        $order = $request->input('order');

        foreach ($order as $index => $clientId) {
            ClientUserOrder::updateOrCreate(
                ['user_id' => $userId, 'client_id' => $clientId],
                ['order' => $index + 1]
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        session('clienteId') != null ? $clienteId = session('clienteId') : $clienteId = null;
        session('projectId') != null ? $projectId = session('projectId') : $projectId = null;

        $campanias = [];

        $gestores = User::whereIn('access_level_id',[2, 3, 4])->where('inactive', 0)->get();
        $comerciales = User::Where('access_level_id',6)->where('inactive', 0)->get();
        $clientes = Client::where('is_client',true)->orderBy('id', 'asc')->get();
        $formasPago = PaymentMethod::all();

        if(isset($clienteId)){
            $gestorId = Client::find($clienteId)->gestor->id ?? null;
            $campanias = Project::where('client_id', $clienteId)->get();
        }else{
            $gestorId = null;
        }


        return view('budgets.create', compact('gestores','comerciales', 'clientes', 'campanias', 'formasPago', 'clienteId','gestorId', 'projectId'));
    }

    public function createFromPetition(string $id)
    {
        $petitionId =  $id;
        $petition = Petition::find($id);

        session('clienteId') != null ? $clienteId = session('clienteId') : $clienteId = $petition->client_id;
        session('projectId') != null ? $projectId = session('projectId') : $projectId = null;

        $campanias = [];
        $clientes = Client::where('is_client',true)->orderBy('id', 'asc')->get();
        $gestores = User::all();
        $formasPago = PaymentMethod::all();

        if(isset($clienteId)){
            $gestorId = Client::find($clienteId)->gestor->id ?? null;
            $campanias = Project::where('client_id', $clienteId)->get();
        }else{
            $gestorId = null;
        }

        return view('budgets.create', compact('gestores', 'clientes', 'campanias', 'formasPago',
                                                         'clienteId','gestorId', 'projectId','petitionId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los campos
        $data = $this->validate($request, [
            'client_id' => 'required|integer',
            'project_id' => 'required|integer',
            'admin_user_id' => 'required|integer',
            'concept' => 'required|max:200',
            'commercial_id' => 'nullable|integer',
            'payment_method_id' => 'nullable|integer',
            'description' => 'nullable',
            'note' => 'nullable',

        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'project_id.required' => 'La campaña es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
        ]);

        $budgetTemporal = Budget::where('temp', true)->orderBy('created_at', 'desc')->first();
        $referenceTemp = $budgetTemporal === null ? 'temp_00' : $this->generateReferenceTemp($budgetTemporal->reference);

        $data['temp'] = true;
        $data['budget_status_id'] = 1;
        $data['reference'] = $referenceTemp;
        $data['creation_date'] = Carbon::now();
        $petitionId = $request->petitionId;

        $budgetCreado = Budget::create($data);
        if($budgetCreado && $petitionId){
            $petition = Petition::find($petitionId);
            if($petition->client_id == $budgetCreado->client_id){
                $petition->finished = true;
                $petition->update();
            }
        }
        return redirect(route('presupuesto.edit', $budgetCreado->id));

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $presupuesto = Budget::find($id);
        $empresa = CompanyDetails::find(1);
        $BudgetConcepts = BudgetConcept::where('budget_id', $presupuesto->id)->get();


        return view('budgets.show', compact('presupuesto','empresa','BudgetConcepts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rutaPrevia = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        session()->put('presupuesto_id', $id);

        // Verificar si la ruta comienza con 'presupuestos.index'
        if ($rutaPrevia && str_starts_with($rutaPrevia, 'presupuestos.index')) {
            session()->put('ruta_previa', $rutaPrevia);
        }

        $presupuesto = Budget::find($id);
        $clientes = Client::where('is_client',true)->orderBy('id', 'asc')->get();

        $gestores = User::all();
        if (!$presupuesto) {
            return redirect()->route('presupuestos.index')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El presupuesto no existe'
            ]);
        }
        $campanias = Project::where('client_id', $presupuesto->client_id)->get();
        $formasPago = PaymentMethod::all();
        $estadoPresupuesto = BudgetStatu::all();
        $budgetConcepts = BudgetConcept::where("budget_id", $id)->get();
        $thisBudgetStatus = BudgetStatu::where('id',$presupuesto->budget_status_id)->get()->first();

        if($presupuesto->budget_status_id == 7 && $presupuesto->total > 0){
            $totalFacturado = Invoice::where('budget_id',$presupuesto->id)->get()->sum('total');
            $porcentaje = ($totalFacturado / $presupuesto->total) * 100;
        }else{ $porcentaje = 0;}

        session('projectId') != null ? $projectId = session('projectId') : $projectId = null;

        return view('budgets.edit', compact('projectId','presupuesto', 'campanias', 'gestores', 'formasPago','estadoPresupuesto', 'budgetConcepts','thisBudgetStatus','clientes','porcentaje'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budget = Budget::find($id);
        // Validación

        $request->validate([
            'client_id' => 'required',
            'project_id' => 'required',
            'admin_user_id' => 'required',
            'payment_method_id' => 'required'
        ]);

        // Formulario datos
        $data = $request->all();
        $thisBudgetConcepts = BudgetConcept::where('budget_id', $budget->id)->get();
        if ($thisBudgetConcepts->isEmpty()) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Para guardar el presupuesto debe tener al menos un concepto.'
            ]);
        }

        // Comprobar existencia a la hora de guardar por si se eliminó un registro durante la creación
        $clientID = $data['client_id'];
        $projectID = $data['project_id'];
        $adminUserID = $data['admin_user_id'];

        $projectExists = Project::where('id', $projectID)->get()->first();

        if( !$projectExists){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'La campaña seleccinada no existe. Es posible que se borrase durante el proceso de creación. Por favor, recargue la página.'
            ]);
        }

        $clientExists = Client::where('id', $clientID)->get()->first();

        if( !$clientExists){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El cliente seleccinado no existe. Es posible que se borrase durante el proceso de creación. Por favor, recargue la página.'
            ]);
        }

        $adminUserExists = User::where('id', $adminUserID)->get()->first();

        if( !$adminUserExists){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El gestor seleccinado no existe. Es posible que se borrase durante el proceso de creación. Por favor, recargue la página.'
            ]);
        }

        // Dates
        if(isset($data['creation_date'])){
            if ($data['creation_date'] != null){
                $data['creation_date'] = date('Y-m-d', strtotime(str_replace('/', '-',  $data['creation_date'])));
            }
        }

        if(!isset($data['iva_percentage'])){
            $data['iva_percentage'] = $request->iva_percentage;
            $ivaPercentage = $request->iva_percentage;
        }else{
            $ivaPercentage  = $data['iva_percentage'];
        }

        // Obtener los conceptos con descuento y actualizarlos
        if(isset($data['discount'])){
            $conceptsDiscounts = $data['discount'];
            // Calculo los valores del presupuesto y de sus conceptos (descuento, total, etc)
            $updateBudgetQuantities = $this->updateBudgetQuantities($budget, $conceptsDiscounts,  $ivaPercentage);
        }else {
            $conceptsDiscounts = 0;
            // Calculo los valores del presupuesto y de sus conceptos (descuento, total, etc)
            $updateBudgetQuantities = $this->updateBudgetQuantities($budget, $conceptsDiscounts,  $ivaPercentage);
        }

        if(!$data['iva']){
            $data['iva'] = $updateBudgetQuantities['iva'];
        }
        $data['discount'] = $updateBudgetQuantities['discount'];
        $data['gross'] = $updateBudgetQuantities['gross'];
        $data['base'] = $updateBudgetQuantities['base'];
        $data['total'] = $data['base'] + $data['iva'];


        if($budget->temp == 1 ){
            $referencia = $this->generateBudgetReference();
            if ($referencia === null) {
                return redirect()->back()->with('toast', [
                    'icon' => 'error',
                    'mensaje' => 'Error al actualizar el presupuesto.'
                ]);
            }
            $data['temp'] = 0;
            $data['reference'] = $referencia['reference'];
            $data['reference_autoincrement_id'] = $referencia['id'];
        }


        $budgetupdated=$budget->update($data);
        $budget->cambiarEstadoPresupuesto($budget->budget_status_id);

        if( str_starts_with($budget->reference, 'temp_')){
            $referencia = $this->generateBudgetReference();
            $data['reference'] = $referencia['reference'];
            $data['reference_autoincrement_id'] = $referencia['id'];
            $budgetupdated=$budget->update($data);
        }

        if($budgetupdated){
           $rutaPrevia = session()->get('ruta_previa','presupuestos.index');

            return response()->json([
                'success' => true,
                'message' => 'Presupuesto actualizado correctamente.',
                'redirect' => Route($rutaPrevia)
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el presupuesto.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        if ($id != null) {
            $budget = Budget::find($id);

            if ($budget != null) {
                // Actualizar 'reference' si 'temp' es true
                if ($budget->temp) {
                    $budgetsDeleted = Budget::onlyTrashed()->where('reference', 'like', 'delete_%')->orderBy('deleted_at', 'desc')->first();
                    $newReference = $budgetsDeleted === null ? 'delete_00' : $this->generateReferenceDelete($budgetsDeleted->reference);
                    $budget->reference = $newReference;
                    $budget->temp = 0;
                    $budget->save();
                }

                // Eliminar todos los conceptos relacionados con el presupuesto
                $budgetConcepts = BudgetConcept::where('budget_id', $budget->id)->get();
                if (count($budgetConcepts) > 0) {
                    foreach ($budgetConcepts as $budgetConcept) {
                        $budgetConcept->delete();
                    }
                }
                // Eliminar el presupuesto
                $budget->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El presupuesto fue borrado con éxito."
                ]);

            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra presupuesto."
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function createFromProject(Client $cliente)
    {
        $id = $cliente->id;
        return view('budgets.create', compact('id'));
    }

    /**
     * Aceptar Presupuesto
     */
    public function aceptarPresupuesto(Request $request)
    {
        $id = $request->id;
        $budget = Budget::find($id);

        $referencia = $this->generateBudgetReference();



        if ($referencia === null) {
            return response(500);
        }
        if($budget->temp == 1 ){
            $budget->temp = 0;
            $budget->reference = $referencia['reference'];
            $budget->reference_autoincrement_id = $referencia['id'];
        }
        $budget->budget_status_id = 3;
        $budget->save();
        if(!$budget->cliente->is_client){
            $budget->cliente->is_client = true;
            $budget->cliente->save();
        }

        return response(200);
        // session()->flash('toast', [
        //     'icon' => 'success',
        //     'mensaje' => 'El presupuesto cambio su estado a Aceptado'
        // ]);
        // return redirect(route('presupuesto.edit', $id));
    }
    /**
     * Cancelar Presupuesto
     */
    public function cancelarPresupuesto(Request $request)
    {
        $id = $request->id;
        $budget = Budget::find($id);

        if ($budget->budget_status_id != 4 ) {
            $budget->budget_status_id = 4;
            $budget->temp = 0;
        }
        if( str_starts_with($budget->reference, 'temp_')){
            $budgetcanleded = Budget::where('budget_status_id', 4)
            ->where('reference', 'like', 'Cancelado_%')
            ->orderByRaw('CAST(SUBSTRING(reference, 11) AS UNSIGNED) DESC')
            ->first();
                    $referenceCancel = $budgetcanleded === null ? 'Cancelado_00' : $this->generateReferenceCancelado($budgetcanleded->reference);
            $budget->reference = $referenceCancel;
        }
        $cancelado = $budget->save();
        $budget->cambiarEstadoPresupuesto($budget->budget_status_id);
        if($cancelado){
            return response( [
                'icon' => 'success',
                'mensaje' => 'El presupuesto cambio su estado a cancelado.'
            ]);
        }else{
            return response( [
                'icon' => 'error',
                'mensaje' => 'El presupuesto no pudo cambiar su estado a cancelado.'
            ]);
        }
    }

    public function duplicate(string $id){
        $budget = Budget::find($id);
        $duplicationSuccess = true;
        //  No se puede duplicar un presupuesto temporal
        if($budget->temp){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Un presupuesto temporal no se puede duplicar'
            ]);
        }
        // No se puede duplicar un presupuesto sin conceptos
        $budgetHasConcepts = $this->budgetHasConcepts($budget);
        if(!$budgetHasConcepts){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Un presupuesto sin conceptos no se puede duplicar'
            ]);
        }
        $newBudget = $budget->replicate();
        // Reference
        $referenceGenerationResult = $this->generateBudgetReference();

        if($referenceGenerationResult['budget_reference_autoincrements']['year'] != ''){
            $dataAutoReference['year'] = $referenceGenerationResult['budget_reference_autoincrements']['year'];
        }else{
            $dataAutoReference['year'] = null ;
        }

        if($referenceGenerationResult['budget_reference_autoincrements']['month_num']  != ''){
            $dataAutoReference['month_num'] = $referenceGenerationResult['budget_reference_autoincrements']['month_num'];
        }else{
            $dataAutoReference['month_num'] = null ;
        }

        $reference =  $referenceGenerationResult['reference'];
        $newBudget->reference = $reference;
        $budgetAutoRefCreate = BudgetReferenceAutoincrement::create($dataAutoReference);
        $newBudget->reference_autoincrement_id = $budgetAutoRefCreate->id;
        $newBudget->creation_date = Carbon::now()->format('Y-m-d');
        $budgetDuplicated = $newBudget->save();

        if($budgetDuplicated){
            //////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////              ESTADO               ///////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            // Inicializo la variable newStatus con el estado que llevará el duplicado
            $newStatus = BudgetStatu::PENDING_CONFIRMATION;
            // Si el presupuesto tiene conceptos de proveedor
            $budgetHasSupplierTypeConcepts = $this->budgetHasSupplierTypeConcepts($budget);
            // Si el presupuesto tiene conceptos propios
            $budgetHasOwnTypeConcepts = $this->budgetHasOwnTypeConcepts($budget);
            // Si no tiene conceptos de proveedor y sí conceptos propios el estado es pendiente de aceptar
             if(!$budgetHasSupplierTypeConcepts){
                if($budgetHasOwnTypeConcepts){
                    $newStatus = BudgetStatu::PENDING_ACCEPT;
                }
            }
            // Si tiene conceptos de proveedor recorrerlos y comprobar si todos tienen favorito o no
            if($budgetHasSupplierTypeConcepts){
                $budgetHasSuppliersAccepted =   $this->budgetHasSuppliersAccepted($budget);
                if($budgetHasSuppliersAccepted){
                    $newStatus = BudgetStatu::PENDING_ACCEPT;
                }else{
                    $newStatus = BudgetStatu::PENDING_CONFIRMATION;
                }
            }
            // Actualizo estado del presupuesto
            $newBudgetStatusUpdate = Budget::where('id', $newBudget->id )->update(array('budget_status_id' =>  $newStatus));
            //////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////      DUPLICAR CONCEPTOS PROPIOS   ///////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            if($budgetHasOwnTypeConcepts){
                // Obtener los conceptos propios de presupuesto
                $thisBudgetOwnTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id',BudgetConceptType::TYPE_OWN )->get();
                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                   $conceptOwnDuplicated =  $thisBudgetOwnTypeConcept->replicate();
                   $conceptOwnDuplicated->budget_id = $newBudget->id;
                   $conceptOwnDuplicated->is_facturado = 0;
                   $conceptOwnDuplicatedSaved = $conceptOwnDuplicated->save();
                   if(!$conceptOwnDuplicatedSaved){
                        $duplicationSuccess = false;
                   }
                }
            }
            //////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////     DUPLICAR CONCEPTOS PROVEEDORES      ////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            // Duplico conceptos de proveedor
            if($budgetHasSupplierTypeConcepts){
                // Obtener los conceptos propios de presupuesto
                $thisBudgetSupplierTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id',BudgetConceptType::TYPE_SUPPLIER )->get();
                if($thisBudgetSupplierTypeConcepts){
                    foreach ($thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept){
                        // Duplicar concepto proveedor
                        $conceptSupplierDuplicated =$thisBudgetSupplierTypeConcept->replicate();
                        $conceptSupplierDuplicated->budget_id = $newBudget->id;
                        $conceptSupplierDuplicated->is_facturado = 0;
                        $conceptSupplierDuplicatedSaved = $conceptSupplierDuplicated->save();
                        if(!$conceptSupplierDuplicatedSaved){
                            $duplicationSuccess = false;
                        }
                        // Duplicar las peticiones del concepto de proveedor   thisBudgetSupplierTypeConcept
                        $supplierRequest1 = BudgetConceptSupplierRequest::where('budget_concept_id', $thisBudgetSupplierTypeConcept->id)->where('option_number', 1)->get()->first();
                        $supplierRequest2 = BudgetConceptSupplierRequest::where('budget_concept_id', $thisBudgetSupplierTypeConcept->id)->where('option_number', 2)->get()->first();
                        $supplierRequest3 = BudgetConceptSupplierRequest::where('budget_concept_id', $thisBudgetSupplierTypeConcept->id)->where('option_number', 3)->get()->first();

                        if($supplierRequest1 && $supplierRequest2 && $supplierRequest3){
                            $supplierRequest1Duplicated = $supplierRequest1->replicate();
                            $supplierRequest1Duplicated->budget_concept_id = $conceptSupplierDuplicated->id;
                            $supplierRequest1DuplicatedSaved = $supplierRequest1Duplicated->save();

                            $supplierRequest2Duplicated =  $supplierRequest2->replicate();
                            $supplierRequest2Duplicated->budget_concept_id = $conceptSupplierDuplicated->id;
                            $supplierRequest2DuplicatedSaved =  $supplierRequest2Duplicated->save();

                            $supplierRequest3Duplicated =  $supplierRequest3->replicate();
                            $supplierRequest3Duplicated->budget_concept_id = $conceptSupplierDuplicated->id;
                            $supplierRequest3DuplicatedSaved =  $supplierRequest3Duplicated->save();
                        }

                        if(!$supplierRequest1DuplicatedSaved || !$supplierRequest2DuplicatedSaved || !$supplierRequest3DuplicatedSaved ){
                            $duplicationSuccess = false;
                        }
                    }
                }
            }
            if($duplicationSuccess ){
                // Si los conceptos están duplicados y el presupuesto también
                return redirect()->route('presupuesto.edit', $newBudget->id)->with('toast', [
                    'icon' => 'success',
                    'mensaje' => 'Presupuesto duplicado.'
                ]);
            }else{
                return redirect()->back()->with('toast', [
                    'icon' => 'error',
                    'mensaje' => 'Error al duplicar el presupuesto.'
                ]);
            }
        }else{
            // El presupuesto no se guardó
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Error al duplicar el presupuesto.'
            ]);
        }
    }

    private function generateReferenceTemp($reference){
         // Extrae los dos dígitos del final de la cadena usando expresiones regulares
         preg_match('/temp_(\d{2})/', $reference, $matches);
        // Incrementa el número primero
        if(count($matches) >= 1){
            $incrementedNumber = intval($matches[1]) + 1;
            // Asegura que el número tenga dos dígitos
            $formattedNumber = str_pad($incrementedNumber, 2, '0', STR_PAD_LEFT);
            // Concatena con la cadena "temp_"
            return "temp_" . $formattedNumber;
        }
    }
    private function generateReferenceCancelado($reference){

         // Extrae los dos dígitos del final de la cadena usando expresiones regulares
         preg_match('/Cancelado_(\d{2})/', $reference, $matches);
        // Incrementa el número primero
        if(count($matches) >= 1){
            $incrementedNumber = intval($matches[1]) + 1;
            // Asegura que el número tenga dos dígitos
            $formattedNumber = str_pad($incrementedNumber, 2, '0', STR_PAD_LEFT);
            // Concatena con la cadena "temp_"
            return "Cancelado_" . $formattedNumber;
        }
    }
    private function generateReferenceDelete($reference){
         // Extrae los dos dígitos del final de la cadena usando expresiones regulares
         preg_match('/delete_(\d{2})/', $reference, $matches);
        // Incrementa el número primero
        if(count($matches) >= 1){
            $incrementedNumber = intval($matches[1]) + 1;
            // Asegura que el número tenga dos dígitos
            $formattedNumber = str_pad($incrementedNumber, 2, '0', STR_PAD_LEFT);
            // Concatena con la cadena "temp_"
            return "delete_" . $formattedNumber;
        }
    }

    public function generateBudgetReference() {
        // Obtener la fecha actual del presupuesto
        $budgetCreationDate = now();
        $datetimeBudgetCreationDate = new \DateTime($budgetCreationDate);

        // Formatear la fecha para obtener los componentes necesarios
        $year = $datetimeBudgetCreationDate->format('Y');
        $monthNum = $datetimeBudgetCreationDate->format('m');

        // Buscar la última referencia autoincremental para el año y mes actual
        $latestReference = BudgetReferenceAutoincrement::where('year', $year)
                            ->where('month_num', $monthNum)
                            ->orderBy('reference_autoincrement', 'desc')
                            ->first();

        // Si no existe, empezamos desde 1, de lo contrario, incrementamos
        $newReferenceAutoincrement = $latestReference ? $latestReference->reference_autoincrement + 1 : 1;

        // Formatear el número autoincremental a 6 dígitos
        $formattedAutoIncrement = str_pad($newReferenceAutoincrement, 6, '0', STR_PAD_LEFT);

        // Crear la referencia
        $reference = $year . '/' . $monthNum . '/' . $formattedAutoIncrement;

        // Guardar o actualizar la referencia autoincremental en BudgetReferenceAutoincrement
        $referenceToSave = new BudgetReferenceAutoincrement([
            'reference_autoincrement' => $newReferenceAutoincrement,
            'year' => $year,
            'month_num' => $monthNum,
            // Otros campos pueden ser asignados si son necesarios
        ]);
        $referenceToSave->save();

        // Devolver el resultado
        return [
            'id' => $referenceToSave->id,
            'reference' => $reference,
            'reference_autoincrement' => $newReferenceAutoincrement,
            'budget_reference_autoincrements' => [
                'year' => $year,
                'month_num' => $monthNum,
                // Añade aquí más si es necesario
            ],
        ];
    }

    /**
     * Calcula recorriendo los conceptos del presupuesto el sumario de este
     *
     * @param  Budget  $budgetToUpdate
     *
     */

    public function updateBudgetQuantities(Budget $budgetToUpdate, $conceptsDiscounts = null, $ivaPercentage = null){

        // Descuentos que hay que aplicar y actualizar en los conceptos
        foreach($conceptsDiscounts as $key => $value){
            if($value > 0){                // Guardar el descuento del concepto y recalcular el total
                // Encuentro el concepto
                $budgetConceptsUdpdateDiscount = BudgetConcept::where('id', $key )->first();
                // Calculo el total
                $totalNoDiscount = $budgetConceptsUdpdateDiscount->total_no_discount;
                $discount = $value;
                $discountToSubstract = ($totalNoDiscount*$discount)/100;
                $newTotal = $totalNoDiscount - $discountToSubstract;
                // Actualizo el concepto con el total y el descuento
                $budgetConceptsUdpdateDiscount->update(array('discount' =>   $discount, 'total' => number_format($newTotal, 2, '.', '')  ));

            }
        }

        // Query para todos los conceptos de este presupuesto
        $thisBudgetConcepts = BudgetConcept::where('budget_id', $budgetToUpdate->id)->get();

        if($thisBudgetConcepts->isEmpty()){
            // Si no hay conceptos que lo ponga a cero todo
            $budgetQuantitiesUdpdated = Budget::where('id', $budgetToUpdate->id )->update(array('total' => 0,'gross' => 0,'base' => 0, 'iva' => 0, 'discount' =>  0 ));
            if($budgetQuantitiesUdpdated){
                return true;
            }else{
                return false;
            }
        }else{

            // Variables que tenemos que ir calculando
            $gross = 0;
            $base = 0;
            $discountQuantity = 0;
            $iva = 0;
            $total = 0;            // Recorro todos los conceptos y voy realizando los calculos
            foreach($thisBudgetConcepts as $concept){
                // Proveedor
                if($concept->concept_type_id == BudgetConceptType::TYPE_SUPPLIER){
                    $purchasePriceWithoutMarginBenefit = $concept->purchase_price;
                    $benefitMargin = $concept->benefit_margin;
                    $marginBenefitToAdd  =  ($purchasePriceWithoutMarginBenefit*$benefitMargin)/100;
                    $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit+ $marginBenefitToAdd;
                    $gross += $purchasePriceWithMarginBenefit;
                    $grossFormated =  $gross;
                    $base += $concept->total ;
                    $baseFormated = $base;
                    $salePrice =  $concept->sale_price;
                    $discountPercentage = $concept->discount;
                    if($discountPercentage > 0){
                        $discountQuantity += (($purchasePriceWithMarginBenefit)*$discountPercentage)/100 ;
                        $discountOfThisConcept =  (($purchasePriceWithMarginBenefit)*$discountPercentage)/100 ;
                        $discountQuantityFormated = $discountQuantity;
                    }else{
                        $discountQuantity += 0;
                    }
                    $iva += ($concept->total * $ivaPercentage) / 100;
                    $ivaFormated =  $iva*100/100;
                }
                // Propio
                if($concept->concept_type_id == BudgetConceptType::TYPE_OWN){
                    $gross += $concept->total_no_discount;
                    $base += $concept->total;
                    $discountPercentage = $concept->discount;
                    if($discountPercentage > 0){
                        $discountQuantity += ($concept->total_no_discount*$discountPercentage)/100 ;

                    }else{
                        $discountQuantity += 0;
                    }
                    // Calculo el IVA
                    $iva += ($concept->total * $ivaPercentage) / 100;
                }
            }
            // Calculo el total
            $total += $iva + $base;
            $totalFormated = $total*100/100;
            $totalFormated =  number_format((float)$totalFormated, 2, '.', '');

            $newBudgetQuantitiesToUpdateArray = [
                'gross' => $gross,
                'base' => $base,
                'discount' => $discountQuantity,
                'iva' => $iva,
                'totalFormated' => $total,
            ];
        return $newBudgetQuantitiesToUpdateArray;
        }
    }

    public function generateInvoiceReference(Budget $budget){
        // Obtener la fecha actual del presupuesto

        $budgetCreationDate = now();
        $datetimeBudgetCreationDate = new \DateTime($budgetCreationDate);

        // Formatear la fecha para obtener los componentes necesarios
        $year = $datetimeBudgetCreationDate->format('Y');
        $monthNum = $datetimeBudgetCreationDate->format('m');

        // Buscar la última referencia autoincremental para el año y mes actual
        $latestReference = InvoiceReferenceAutoincrement::where('year', $year)
                            ->whereNull('ceuta')
                            ->where('month_num', $monthNum)
                            ->orderBy('id', 'desc')
                            ->first();
        // Si no existe, empezamos desde 1, de lo contrario, incrementamos
        $newReferenceAutoincrement = $latestReference ? $latestReference->reference_autoincrement + 1 : 1;
        // Formatear el número autoincremental a 6 dígitos
        $formattedAutoIncrement = str_pad($newReferenceAutoincrement, 4, '0', STR_PAD_LEFT);
        // Crear la referencia
        switch ($monthNum) {
            case 1:
                $monthLetter = 'A';
            break;
            case 2:
                $monthLetter = 'B';
            break;
            case 3:
                $monthLetter = 'C';
            break;
            case 4:
                $monthLetter = 'D';
            break;
            case 5:
                $monthLetter = 'E';
            break;
            case 6:
                $monthLetter = 'F';
            break;
            case 7:
                $monthLetter = 'G';
            break;
            case 8:
                $monthLetter = 'H';
            break;
            case 9:
                $monthLetter = 'I';
            break;
            case 10:
                $monthLetter = 'J';
            break;
            case 11:
                $monthLetter = 'K';
            break;
            case 12:
                $monthLetter = 'L';
            break;
        }
        $reference = $monthLetter.$year . '-'. $formattedAutoIncrement;
        // Guardar o actualizar la referencia autoincremental en BudgetReferenceAutoincrement
        $referenceToSave = new InvoiceReferenceAutoincrement([
            'reference_autoincrement' => $newReferenceAutoincrement,
            'year' => $year,
            'month_num' => $monthNum,
            'letter_months' => $monthLetter,
        ]);

        $referenceToSave->save();

        return [
            'id' => $referenceToSave->id,
            'reference' => $reference,
            'reference_autoincrement' => $newReferenceAutoincrement,
            'budget_reference_autoincrements' => [
                'year' => $year,
                'month_num' => $monthNum,
            ],
        ];
    }

    public function generateInvoiceReferenceCeuta(Budget $budget){
        // Obtener la fecha actual del presupuesto
        $budgetCreationDate = now();
        $datetimeBudgetCreationDate = new \DateTime($budgetCreationDate);

        // Formatear la fecha para obtener los componentes necesarios
        $year = $datetimeBudgetCreationDate->format('Y');
        $monthNum = $datetimeBudgetCreationDate->format('m');

        // Buscar la última referencia autoincremental para el año y mes actual
        $latestReference = InvoiceReferenceAutoincrement::where('year', $year)
                            ->where('ceuta', 1)
                            ->where('month_num', $monthNum)
                            ->orderBy('id', 'desc')
                            ->first();
        // Si no existe, empezamos desde 1, de lo contrario, incrementamos
        $newReferenceAutoincrement = $latestReference ? $latestReference->reference_autoincrement + 1 : 1;
        // Formatear el número autoincremental a 6 dígitos
        $formattedAutoIncrement = str_pad($newReferenceAutoincrement, 4, '0', STR_PAD_LEFT);
        // Crear la referencia
        switch ($monthNum) {
            case 1:
                $monthLetter = 'A';
            break;
            case 2:
                $monthLetter = 'B';
            break;
            case 3:
                $monthLetter = 'C';
            break;
            case 4:
                $monthLetter = 'D';
            break;
            case 5:
                $monthLetter = 'E';
            break;
            case 6:
                $monthLetter = 'F';
            break;
            case 7:
                $monthLetter = 'G';
            break;
            case 8:
                $monthLetter = 'H';
            break;
            case 9:
                $monthLetter = 'I';
            break;
            case 10:
                $monthLetter = 'J';
            break;
            case 11:
                $monthLetter = 'K';
            break;
            case 12:
                $monthLetter = 'L';
            break;
        }
        $reference = 'CE/' . $monthLetter.$year . '-'. $formattedAutoIncrement;
        // Guardar o actualizar la referencia autoincremental en BudgetReferenceAutoincrement
        $referenceToSave = new InvoiceReferenceAutoincrement([
            'reference_autoincrement' => $newReferenceAutoincrement,
            'year' => $year,
            'month_num' => $monthNum,
            'letter_months' => $monthLetter,
            'ceuta' => 1,
        ]);

        $referenceToSave->save();

        return [
            'id' => $referenceToSave->id,
            'reference' => $reference,
            'reference_autoincrement' => $newReferenceAutoincrement,
            'budget_reference_autoincrements' => [
                'year' => $year,
                'month_num' => $monthNum,
            ],
        ];
    }

    //Comprueba si el presupuesto tiene conceptos
    public function budgetHasConcepts(Budget $budget){
        // Query para todos los conceptos de este presupuesto
        $thisBudgetConcepts = BudgetConcept::where('budget_id', $budget->id)->get();
        // Comprobar si el resultado está vacío o no
        if($thisBudgetConcepts->isEmpty()){
            return false;
        }else{
            return true;
        }
    }
    public function budgetHasOwnTypeConcepts(Budget $budget)
    {
        $thisBudgetOwnTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id',BudgetConceptType::TYPE_OWN )->get();
        // Comprobar si el resultado está vacío o no
        if($thisBudgetOwnTypeConcepts->isEmpty()){
            return false;
        }else{
            return true;
        }
    }


    public function budgetHasSupplierTypeConcepts(Budget $budget)
    {
        $thisBudgetSupplierTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id',BudgetConceptType::TYPE_SUPPLIER )->get();
        // Comprobar si el resultado está vacío o no
        if($thisBudgetSupplierTypeConcepts->isEmpty()){
            return false;
        }else{
            return true;
        }
    }

    public function budgetHasSuppliersAccepted(Budget $budget)
    {        // Primero ver si tiene conceptos de proveedor
        $budgetHasSupplierTypeConcepts = $this->budgetHasSupplierTypeConcepts($budget);
        if(!$budgetHasSupplierTypeConcepts){
            return true;
        }else{
            // Obtener todos los conceptos de proveedor del presupuesto
            //$budgetSupplierConcepts = BudgetConcepts::where('concept_type_id', BudgetConceptType::TYPE_SUPPLIER)->get();
            $budgetSupplierConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id', BudgetConceptType::TYPE_SUPPLIER)->get();
            // Recorrer peticiones y ver si de cada concepto tiene un seleccionado
            foreach ($budgetSupplierConcepts as $supplierConcept){
                $supplierRequestAccepted = BudgetConceptSupplierRequest::where('budget_concept_id', $supplierConcept->id)->where('selected', 1)->get()->first();
                if($supplierRequestAccepted == null){
                    return false;
                }
            }
            return true;
        }
    }
    public function generateInvoice(Request $request){
        $budget = Budget::find($request->id);


        $generationSuccess = true;

        //  No se puede generar factura un presupuesto temporal
         if($budget->temp){

            return response()->json([
                'status' => false,
                'mensaje' => "Un presupuesto temporal no puede generar factura."
            ]);
        }

        // No se puede generar factura de un presupuesto sin conceptos
        $budgetHasConcepts = $this->budgetHasConcepts($budget);
        if(!$budgetHasConcepts){
            return response()->json([
                'status' => false,
                'mensaje' => "Un presupuesto sin conceptos no puede generar factura."
            ]);
        }

        // Validación campos array data
        if( $budget->discount_percentage){
            $discountPercentage =  $budget->discount_percentage;
        }else{
            $discountPercentage = 0;
        }

        if($budget->is_ceuta){
            $referenceGenerationResult = $this->generateInvoiceReferenceCeuta($budget);
        }else{
            $referenceGenerationResult = $this->generateInvoiceReference($budget);
        }

        $grossfacturado=0;
        $basefacturada=0;
        $descuento=0;
        $ivaTotalfacturado=0;
        $totalfacturado=0;

        if(count(BudgetConcept::where('budget_id', $budget->id)->where('is_facturado', true)->get()) >= 1){

            $budgetConcepts = BudgetConcept::where('budget_id', $budget->id)->where('is_facturado', true)->get();


            foreach ($budgetConcepts as $key => $concept) {
                // Si el concepto es PROVEEDOR
                if ($concept->concept_type_id === 1) {
                    if ($concept->discount === null) {
                        $grossConcept = $concept->sale_price;
                        $baseConcept = $grossConcept;
                        $grossfacturado += $grossConcept;
                        $basefacturada += $baseConcept;
                    }else {
                        $grossConcept =  $concept->sale_price;
                        $descuentoConcept = $concept->discount;
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        $descuento += $importeConceptDescuento;
                        $grossfacturado += $grossConcept;
                        $basefacturada += $baseConcept;
                    }
                }
                elseif($concept->concept_type_id === 2){
                    if ($concept->discount === null) {
                        $grossConcept = $concept->units * $concept->sale_price;
                        $baseConcept = $grossConcept;
                        $grossfacturado += $grossConcept;
                        $basefacturada += $baseConcept;
                    }else {
                        $grossConcept = $concept->units * $concept->sale_price;
                        $descuentoConcept = $concept->discount;
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        $descuento += $importeConceptDescuento;
                        $grossfacturado += $grossConcept;
                        $basefacturada += $baseConcept;
                    }
                }
            }
            // Calculamos el Iva y el Total
            $ivaTotalfacturado += ( $basefacturada * 21 ) /100;
            $totalfacturado += $basefacturada + $ivaTotalfacturado;

        }


        if($budget->budget_status_id = 7 || $budget->budget_status_id = 6){
            $totalFacturado = Invoice::where('budget_id',$budget->id)->get()->sum('total');
            $porcentaje = 1 - ($totalFacturado / $budget->total);
            if($porcentaje == 0){
                return response()->json([
                    'status' => false,
                    'mensaje' => "Ya se generaron facturas por el valor total del presupuesto"
                ]);
            }
        }else{ $porcentaje = 1;}

        $data = [
            'budget_id' => $budget->id,
            'reference' => $referenceGenerationResult['reference'],
            'reference_autoincrement_id' => $referenceGenerationResult['id'],
            'admin_user_id' => Auth::user()->id ?? 1,
            'client_id' => $budget->client_id,
            'project_id' => $budget->project_id,
            'payment_method_id' => $budget->payment_method_id,
            'invoice_status_id' => 1, //abierta
            'concept' => $budget->concept,
            'gross' => ($budget->gross - $grossfacturado ) * $porcentaje,
            'base' => ($budget->base - $basefacturada) * $porcentaje,
            'iva' => ($budget->iva - $ivaTotalfacturado) * $porcentaje,
            'iva_percentage' => ($budget->iva_percentage),
            'discount' => ($budget->discount - $descuento) * $porcentaje,
            'discount_percentage' => $discountPercentage,
            'total' => ($budget->total - $totalfacturado) * $porcentaje,
            'is_ceuta' => $budget->is_ceuta,

        ];


        // Creación de la factura
        $invoice = Invoice::create($data);

        if(!isset($invoice)){
            return response()->json([
                'status' => false,
                'mensaje' => "Error en la creacion de la factura. "
            ]);
        }

        if(isset($invoice)){
            $budget->budget_status_id = 6;
            $budget->save();
            //////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////         CONCEPTOS PROPIOS         ///////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            $budgetHasOwnTypeConcepts = $this->budgetHasOwnTypeConcepts($budget);

            if($budgetHasOwnTypeConcepts){
                // Obtener los conceptos propios de presupuesto
                $thisBudgetOwnTypeConcepts = BudgetConcept::where('budget_id', $budget->id)
                    ->where('is_facturado', false)
                    ->where('concept_type_id',BudgetConceptType::TYPE_OWN )
                    ->get();

                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                    $conceptOwnData = [
                        'invoice_id' => $invoice->id,
                        'concept_type_id' => BudgetConceptType::TYPE_OWN,
                        'service_id' => $thisBudgetOwnTypeConcept->service_id,
                        'services_category_id' => $thisBudgetOwnTypeConcept->services_category_id,
                        'title' => $thisBudgetOwnTypeConcept->title,
                        'concept' => $thisBudgetOwnTypeConcept->concept,
                        'units' => $thisBudgetOwnTypeConcept->units,
                        'purchase_price' => $thisBudgetOwnTypeConcept->purchase_price,
                        'benefit_margin' => $thisBudgetOwnTypeConcept->benefit_margin,
                        'sale_price' => $thisBudgetOwnTypeConcept->sale_price * $porcentaje,
                        'discount' => $thisBudgetOwnTypeConcept->discount * $porcentaje,
                        'total' => $thisBudgetOwnTypeConcept->total * $porcentaje,
                        'total_no_discount' => $thisBudgetOwnTypeConcept->total_no_discount * $porcentaje,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ];

                    $conceptOwnCreate = InvoiceConcepts::create($conceptOwnData);
                    $conceptOwnSaved = $conceptOwnCreate->save();
                    $thisBudgetOwnTypeConcept->update(['is_facturado' => true]);

                    if(!$conceptOwnSaved){
                            $generationSuccess = false;
                    }
                }
            }

            if(!$generationSuccess){
                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                    $thisBudgetOwnTypeConcept->delete();
                }
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error al generar factura en conceptos propios."
                ]);
            }

            //////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////         CONCEPTOS PROVEEDORES           ////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////

            $budgetHasSupplierTypeConcepts = $this->budgetHasSupplierTypeConcepts($budget);

            if($budgetHasSupplierTypeConcepts){
                // Obtener los conceptos de proveedor de presupuesto
                $thisBudgetSupplierTypeConcepts = BudgetConcept::where('budget_id', $budget->id)
                ->where('concept_type_id', BudgetConceptType::TYPE_SUPPLIER )
                ->where('is_facturado', false)
                ->get();

                if($thisBudgetSupplierTypeConcepts){
                    foreach ($thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept){
                        $conceptSupplierData = [
                            'invoice_id' => $invoice->id,
                            'concept_type_id' => BudgetConceptType::TYPE_SUPPLIER,
                            'service_id' => $thisBudgetSupplierTypeConcept->service_id,
                            'services_category_id' => $thisBudgetSupplierTypeConcept->services_category_id,
                            'title' => $thisBudgetSupplierTypeConcept->title,
                            'concept' => $thisBudgetSupplierTypeConcept->concept,
                            'units' => $thisBudgetSupplierTypeConcept->units,
                            'purchase_price' => $thisBudgetSupplierTypeConcept->purchase_price,
                            'benefit_margin' => $thisBudgetSupplierTypeConcept->benefit_margin,
                            'sale_price' => $thisBudgetSupplierTypeConcept->sale_price * $porcentaje,
                            'discount' => $thisBudgetSupplierTypeConcept->discount * $porcentaje,
                            'total' => $thisBudgetSupplierTypeConcept->total * $porcentaje,
                            'total_no_discount' => $thisBudgetSupplierTypeConcept->total_no_discount * $porcentaje,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ];

                        $conceptSupplierCreate = InvoiceConcepts::create($conceptSupplierData);
                        $conceptSupplierSaved = $conceptSupplierCreate->save();
                        $thisBudgetSupplierTypeConcept->update(['is_facturado' => true]);

                        if(!$conceptSupplierSaved){
                                $generationSuccess = false;
                        }
                    }
                }
            }

            if(!$generationSuccess){
                foreach( $thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept ){
                    $thisBudgetSupplierTypeConcept->delete();
                }
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error al generar factura en conceptos proveedor."
                ]);

            }

            // Respuesta
            return response()->json([
                'status' => true,
                'mensaje' => "Factura generada correctamente"
            ]);
        }
    }

    public function generateInvoiceConcept(Request $request){
        $budget = Budget::find($request->id);

        $generationSuccess = true;

        //  No se puede generar factura un presupuesto temporal
         if($budget->temp){

            return response()->json([
                'status' => false,
                'mensaje' => "Un presupuesto temporal no puede generar factura."
            ]);
        }

        // No se puede generar factura de un presupuesto sin conceptos
        $budgetHasConcepts = $this->budgetHasConcepts($budget);
        if(!$budgetHasConcepts){
            return response()->json([
                'status' => false,
                'mensaje' => "Un presupuesto sin conceptos no puede generar factura."
            ]);
        }

        // Validación campos array data
        if( $budget->discount_percentage){
            $discountPercentage =  $budget->discount_percentage;
        }else{
            $discountPercentage = 0;
        }

        if($budget->is_ceuta){
            $referenceGenerationResult = $this->generateInvoiceReferenceCeuta($budget);
        }else{
            $referenceGenerationResult = $this->generateInvoiceReference($budget);
        }

        if($budget->budget_status_id = 7 || $budget->budget_status_id = 6){
            $totalFacturado = Invoice::where('budget_id',$budget->id)->get()->sum('total');
            $porcentaje = 1 - ($totalFacturado / $budget->total);
            if($porcentaje == 0){
                return response()->json([
                    'status' => false,
                    'mensaje' => "Ya se generaron facturas por el valor total del presupuesto"
                ]);
            }
        }else{ $porcentaje = 1;}
        $data = [
            'budget_id' => $budget->id,
            'reference' => $referenceGenerationResult['reference'],
            'reference_autoincrement_id' => $referenceGenerationResult['id'],
            'admin_user_id' => Auth::user()->id ?? 1,
            'client_id' => $budget->client_id,
            'project_id' => $budget->project_id,
            'payment_method_id' => $budget->payment_method_id,
            'invoice_status_id' => 1, //abierta
            'concept' => $budget->concept,
            'gross' => $budget->gross * $porcentaje,
            'base' => $budget->base * $porcentaje,
            'iva' => $budget->iva * $porcentaje,
            'iva_percentage' => $budget->iva_percentage,
            'discount' => $budget->discount * $porcentaje,
            'discount_percentage' => $discountPercentage,
            'total' => $budget->total * $porcentaje,
            'is_ceuta' => $budget->is_ceuta,

        ];

        // Creación de la factura
        $invoice = Invoice::create($data);

        if(!isset($invoice)){
            return response()->json([
                'status' => false,
                'mensaje' => "Error en la creacion de la factura. "
            ]);
        }

        if(isset($invoice)){
            $budget->budget_status_id = 6;
            $budget->save();
            //////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////         CONCEPTOS PROPIOS         ///////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            $budgetHasOwnTypeConcepts = $this->budgetHasOwnTypeConcepts($budget);

            if($budgetHasOwnTypeConcepts){
                // Obtener los conceptos propios de presupuesto
                $thisBudgetOwnTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id',BudgetConceptType::TYPE_OWN )->get();

                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                    $conceptOwnData = [
                        'invoice_id' => $invoice->id,
                        'concept_type_id' => BudgetConceptType::TYPE_OWN,
                        'service_id' => $thisBudgetOwnTypeConcept->service_id,
                        'services_category_id' => $thisBudgetOwnTypeConcept->services_category_id,
                        'title' => $thisBudgetOwnTypeConcept->title,
                        'concept' => $thisBudgetOwnTypeConcept->concept,
                        'units' => $thisBudgetOwnTypeConcept->units,
                        'purchase_price' => $thisBudgetOwnTypeConcept->purchase_price,
                        'benefit_margin' => $thisBudgetOwnTypeConcept->benefit_margin,
                        'sale_price' => $thisBudgetOwnTypeConcept->sale_price * $porcentaje,
                        'discount' => $thisBudgetOwnTypeConcept->discount * $porcentaje,
                        'total' => $thisBudgetOwnTypeConcept->total * $porcentaje,
                        'total_no_discount' => $thisBudgetOwnTypeConcept->total_no_discount * $porcentaje,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ];

                    $conceptOwnCreate = InvoiceConcepts::create($conceptOwnData);
                    $conceptOwnSaved = $conceptOwnCreate->save();

                    if(!$conceptOwnSaved){
                            $generationSuccess = false;
                    }
                }
            }

            if(!$generationSuccess){
                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                    $thisBudgetOwnTypeConcept->delete();
                }
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error al generar factura en conceptos propios."
                ]);
            }

            //////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////         CONCEPTOS PROVEEDORES           ////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////

            $budgetHasSupplierTypeConcepts = $this->budgetHasSupplierTypeConcepts($budget);

            if($budgetHasSupplierTypeConcepts){
                // Obtener los conceptos de proveedor de presupuesto
                $thisBudgetSupplierTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id', BudgetConceptType::TYPE_SUPPLIER )->get();

                if($thisBudgetSupplierTypeConcepts){
                    foreach ($thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept){
                        $conceptSupplierData = [
                            'invoice_id' => $invoice->id,
                            'concept_type_id' => BudgetConceptType::TYPE_SUPPLIER,
                            'service_id' => $thisBudgetSupplierTypeConcept->service_id,
                            'services_category_id' => $thisBudgetSupplierTypeConcept->services_category_id,
                            'title' => $thisBudgetSupplierTypeConcept->title,
                            'concept' => $thisBudgetSupplierTypeConcept->concept,
                            'units' => $thisBudgetSupplierTypeConcept->units,
                            'purchase_price' => $thisBudgetSupplierTypeConcept->purchase_price,
                            'benefit_margin' => $thisBudgetSupplierTypeConcept->benefit_margin,
                            'sale_price' => $thisBudgetSupplierTypeConcept->sale_price * $porcentaje,
                            'discount' => $thisBudgetSupplierTypeConcept->discount * $porcentaje,
                            'total' => $thisBudgetSupplierTypeConcept->total * $porcentaje,
                            'total_no_discount' => $thisBudgetSupplierTypeConcept->total_no_discount * $porcentaje,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ];

                        $conceptSupplierCreate = InvoiceConcepts::create($conceptSupplierData);
                        $conceptSupplierSaved = $conceptSupplierCreate->save();

                        if(!$conceptSupplierSaved){
                                $generationSuccess = false;
                        }
                    }
                }
            }

            if(!$generationSuccess){
                foreach( $thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept ){
                    $thisBudgetSupplierTypeConcept->delete();
                }
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error al generar factura en conceptos proveedor."
                ]);

            }

            // Respuesta
            return response()->json([
                'status' => true,
                'mensaje' => "Factura generada correctamente"
            ]);
        }
    }

    public function generateInvoicePartial(Request $request){
        $budget = Budget::find($request->id);
        $porcentaje = $request['percentage'];
        if($porcentaje == 0){
            return response()->json([
                'status' => false,
                'mensaje' => "No es posible generar una factura con el 0% del presupuesto."
            ]);
        }
        $generationSuccess = true;
        //  No se puede generar factura un presupuesto temporal
         if($budget->temp){
            return response()->json([
                'status' => false,
                'mensaje' => "Un presupuesto temporal no puede generar factura."
            ]);
        }
        // No se puede generar factura de un presupuesto sin conceptos
        $budgetHasConcepts = $this->budgetHasConcepts($budget);
        if(!$budgetHasConcepts){
            return response()->json([
                'status' => false,
                'mensaje' => "Un presupuesto sin conceptos no puede generar factura."
            ]);
        }
        // Validación campos array data
        if( $budget->discount_percentage){
            $discountPercentage =  $budget->discount_percentage;
        }else{
            $discountPercentage = 0;
        }
        $total = ($budget->total * $porcentaje) / 100;
        $gross = ($budget->gross * $porcentaje) / 100;
        $base = ($budget->base * $porcentaje) / 100;
        $iva = ($budget->iva * $porcentaje) / 100;
        $discount = ($budget->discount * $porcentaje) / 100;

        $budget->invoiced_advance = $porcentaje;

        if($budget->is_ceuta){
            $referenceGenerationResult = $this->generateInvoiceReferenceCeuta($budget);
        }else{
            $referenceGenerationResult = $this->generateInvoiceReference($budget);
        }

        $data = [
            'budget_id' => $budget->id,
            'reference' => $referenceGenerationResult['reference'],
            'reference_autoincrement_id' => $referenceGenerationResult['id'],
            'admin_user_id' => Auth::user()->id ?? 1,
            'client_id' => $budget->client_id,
            'project_id' => $budget->project_id,
            'payment_method_id' => $budget->payment_method_id,
            'invoice_status_id' => 1, //abierta
            'concept' => $budget->concept,
            'gross' => $gross,
            'base' => $base,
            'iva' => $iva,
            'iva_percentage' => $budget->iva_percentage,
            'discount' => $discount,
            'discount_percentage' => $discountPercentage,
            'total' => $total,
            'partial' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_ceuta' => $budget->is_ceuta,
        ];

        // Creación de la factura
        $invoice = Invoice::create($data);

        $invoiceSaved = $invoice->save();

        if(!$invoiceSaved){
            return response()->json([
                'status' => false,
                'mensaje' => "Error en la creacion de la factura. "
            ]);
        }
        if($invoiceSaved){
            $budget->budget_status_id = 7;
            $budget->save();
            //////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////         CONCEPTOS PROPIOS         ///////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            $budgetHasOwnTypeConcepts = $this->budgetHasOwnTypeConcepts($budget);
            if($budgetHasOwnTypeConcepts){
                // Obtener los conceptos propios de presupuesto
                $thisBudgetOwnTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id',BudgetConceptType::TYPE_OWN )->get();
                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                    $total = ($thisBudgetOwnTypeConcept->total * $porcentaje) / 100;
                    $discount = ($thisBudgetOwnTypeConcept->discount * $porcentaje) / 100;
                    $total_no_discount = ($thisBudgetOwnTypeConcept->total_no_discount * $porcentaje) / 100;
                    $sale_price = ($thisBudgetOwnTypeConcept->sale_price * $porcentaje) / 100;
                    $conceptOwnData = [
                        'invoice_id' => $invoice->id,
                        'concept_type_id' => BudgetConceptType::TYPE_OWN,
                        'service_id' => $thisBudgetOwnTypeConcept->service_id,
                        'services_category_id' => $thisBudgetOwnTypeConcept->services_category_id,
                        'title' => $thisBudgetOwnTypeConcept->title,
                        'concept' => $thisBudgetOwnTypeConcept->concept,
                        'units' => $thisBudgetOwnTypeConcept->units,
                        'purchase_price' => $thisBudgetOwnTypeConcept->purchase_price,
                        'benefit_margin' => $thisBudgetOwnTypeConcept->benefit_margin,
                        'sale_price' => $sale_price,
                        'discount' => $discount,
                        'total' => $total,
                        'total_no_discount' => $total_no_discount,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ];
                    $conceptOwnCreate = InvoiceConcepts::create($conceptOwnData);
                    $conceptOwnSaved = $conceptOwnCreate->save();
                    if(!$conceptOwnSaved){
                            $generationSuccess = false;
                    }
                }
            }
            if(!$generationSuccess){
                foreach( $thisBudgetOwnTypeConcepts as $thisBudgetOwnTypeConcept ){
                    $thisBudgetOwnTypeConcept->delete();
                }
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error al generar factura en conceptos propios."
                ]);
            }
            //////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////         CONCEPTOS PROVEEDORES           ////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            $budgetHasSupplierTypeConcepts = $this->budgetHasSupplierTypeConcepts($budget);
            if($budgetHasSupplierTypeConcepts){
                // Obtener los conceptos de proveedor de presupuesto
                $thisBudgetSupplierTypeConcepts = BudgetConcept::where('budget_id', $budget->id)->where('concept_type_id', BudgetConceptType::TYPE_SUPPLIER )->get();
                if($thisBudgetSupplierTypeConcepts){
                    foreach ($thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept){
                        $total = ($thisBudgetSupplierTypeConcept->total * $porcentaje) / 100;
                        $discount = ($thisBudgetSupplierTypeConcept->discount * $porcentaje) / 100;
                        $total_no_discount = ($thisBudgetSupplierTypeConcept->total_no_discount * $porcentaje) / 100;
                        $sale_price = ($thisBudgetSupplierTypeConcept->sale_price * $porcentaje) / 100;
                        $conceptSupplierData = [
                            'invoice_id' => $invoice->id,
                            'concept_type_id' => BudgetConceptType::TYPE_SUPPLIER,
                            'service_id' => $thisBudgetSupplierTypeConcept->service_id,
                            'services_category_id' => $thisBudgetSupplierTypeConcept->services_category_id,
                            'title' => $thisBudgetSupplierTypeConcept->title,
                            'concept' => $thisBudgetSupplierTypeConcept->concept,
                            'units' => $thisBudgetSupplierTypeConcept->units,
                            'purchase_price' => $thisBudgetSupplierTypeConcept->purchase_price,
                            'benefit_margin' => $thisBudgetSupplierTypeConcept->benefit_margin,
                            'sale_price' => $sale_price,
                            'discount' => $discount,
                            'total' => $total,
                            'total_no_discount' => $total_no_discount,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ];

                        $conceptSupplierCreate = InvoiceConcepts::create($conceptSupplierData);
                        $conceptSupplierSaved = $conceptSupplierCreate->save();

                        if(!$conceptSupplierSaved){
                                $generationSuccess = false;
                        }
                    }
                }
            }
            if(!$generationSuccess){
                foreach( $thisBudgetSupplierTypeConcepts as $thisBudgetSupplierTypeConcept ){
                    $thisBudgetSupplierTypeConcept->delete();
                }
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error al generar factura en conceptos proveedor."
                ]);
            }
            // Respuesta
            return response()->json([
                'status' => true,
                'mensaje' => "Factura parcial generada correctamente"
            ]);
        }
    }

    public function createTask(Request $request){
        $taskSaved = false;
        $budget = Budget::find($request->id);
        //Crear Tarea
        $budgetConcept = BudgetConcept::where('budget_id', $budget->id)->get();
        $empresa = CompanyDetails::find(1);
        foreach($budgetConcept as $con){
            $time_hour = $this->calcNewHoursPrice($con->total, $empresa->price_hour);
            if($con->concept_type_id == 2){
                $budgetCon =  Task::where('budget_concept_id', $con->id)->count();
                if($budgetCon == 0){
                    $dataTask['admin_user_id'] = null;
                    $dataTask['gestor_id'] = Auth::user()->id ?? 1;
                    $dataTask['priority_id'] = null;
                    $dataTask['project_id'] = $budget->project_id;
                    $dataTask['budget_id'] = $budget->id;
                    $dataTask['budget_concept_id'] = $con->id;
                    $dataTask['task_status_id'] = 2;
                    $dataTask['title'] = $con->title;
                    $dataTask['description'] = $con->concept;
                    $dataTask['total_time_budget'] = $time_hour;
                    $dataTask['estimated_time'] = $time_hour;
                    $dataTask['real_time'] = '00:00:00';
                    $task = Task::create($dataTask);
                    $taskSaved = $task->save();
                }else{
                    return response()->json([
                        'status' => false,
                        'mensaje' => "Tareas ya generadas anteriormente."
                    ]);
                }
            }
        }
        if($taskSaved){
            return response()->json([
                'status' => true,
                'mensaje' => "Tareas generadas con exito"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'mensaje' => "Fallo al generar las tareas"
            ]);
        }

    }

    public function calcNewHoursPrice($total, $precio_hora){
        $toMin = $total / $precio_hora;
        $Mins = $toMin * 60;
        $result = (int)($Mins);
		$hours = floor($result / 60).':'.($result -   floor($result / 60) * 60);
        return $hours;
    }

    public function generatePDF(Request $request){

        $budget = Budget::find($request->id);

        $sumatorio = $request->sumatorio;

        $pdf = $this->createPdf($budget,$sumatorio);

        return $pdf->download('presupuesto_' . $budget['reference'] . '_' . Carbon::now()->format('Y-m-d') . '.pdf');

    }

    public function createPdf($budget, $sumatorio){
     // Los conceptos de este presupuesto
     $thisBudgetConcepts = BudgetConcept::where('budget_id', $budget->id)->get();
     // Condiciones de categoría de los servicios
     $conceptCategoriesID = array();
     foreach($thisBudgetConcepts as $concept){
         if(!in_array($concept->services_category_id, $conceptCategoriesID)){
             array_push($conceptCategoriesID, $concept->services_category_id);
         }
     }
     foreach($conceptCategoriesID as $key => $value){
         $category = ServiceCategories::where('id', $value)->get()->first();
         if($category){
             // Definir los conceptos del PDF y precios
             $conceptosPDF = '';
             $precioSinIvaPDF = 0;
             if(count($thisBudgetConcepts) >= 2){
                 foreach($thisBudgetConcepts as $concepto){
                     if ($conceptosPDF == '') {
                         $conceptosPDF = $concepto->title;
                     }else{
                         $conceptosPDF = $conceptosPDF . ', ' . $concepto->title;
                     }
                     $precioSinIvaPDF += $concepto->total;
                 }
             } else {
                 $conceptosPDF = $thisBudgetConcepts[0]->title;
             }
         }
     }
     // Título
     $title = "Presupuesto - ".$budget['reference'];
     // PDF personalización
     $data = [
         'title' => $title,
         'budget_reference' => $budget['reference'],
     ];
     // Array de conceptos para utilizar en la vista, formatea cadenas para que cuadre
     $budgetConceptsFormated = array();
     foreach($thisBudgetConcepts as $budgetConcept){
         // Título
         $budgetConceptsFormated[$budgetConcept->id]['title'] = $budgetConcept['title'];
         // Unidades
         $budgetConceptsFormated[$budgetConcept->id]['units'] = $budgetConcept['units'];
         // Precio
         if($budgetConcept->concept_type_id == BudgetConceptType::TYPE_OWN){
             $budgetConceptsFormated[$budgetConcept->id]['subtotal'] = number_format((float)$budgetConcept->units * $budgetConcept->sale_price, 2, '.', '');
             $budgetConceptsFormated[$budgetConcept->id]['unit_price'] = number_format((float)$budgetConcept->sale_price, 2, '.', '');
         }
         if($budgetConcept->concept_type_id == BudgetConceptType::TYPE_SUPPLIER){
             $purchasePriceWithoutMarginBenefit = $budgetConcept->purchase_price;
             $benefitMargin = $budgetConcept->benefit_margin;
             $marginBenefitToAdd  =  ($purchasePriceWithoutMarginBenefit*$benefitMargin)/100;
             $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit+ $marginBenefitToAdd;
             if( $purchasePriceWithMarginBenefit != null){
                 $budgetConceptsFormated[$budgetConcept->id]['unit_price'] = round((number_format((float)$budgetConcept->purchase_price, 2, '.', '') / $budgetConcept->units / 100 * number_format((float)$budgetConcept->benefit_margin, 2, '.', '')) + (number_format((float)$budgetConcept->purchase_price, 2, '.', '') / $budgetConcept->units), 2);
             }
             $budgetConceptsFormated[$budgetConcept->id]['subtotal'] = number_format((float)$budgetConcept->total_no_discount, 2, '.', '');
         }
         // Descuento
         if($budgetConcept['discount'] == null){
             $budgetConceptsFormated[$budgetConcept->id]['discount'] = "0,00";
         }else{
             $budgetConceptsFormated[$budgetConcept->id]['discount'] = number_format((float)$budgetConcept['discount'], 2, ',', '');
         }
         // Total
         $budgetConceptsFormated[$budgetConcept->id]['total'] = number_format((float)$budgetConcept['total'], 2, ',', '');
         // Descripción
         $rawConcepts = $budgetConcept['concept'];
         // Descripción dividida en cadenas y saltos de linea
         $arrayConceptStringsAndBreakLines =  explode(PHP_EOL, $rawConcepts);
         $budgetConceptsFormated[$budgetConcept->id]['description'] = $arrayConceptStringsAndBreakLines;
     }

     $pdf = PDF::loadView('budgets.previewPDF', compact('budget','data', 'budgetConceptsFormated','sumatorio'));

     return $pdf;

    }

    public function savePDF(Budget $budget)
    {
        // Nombre del archivo basado en la referencia del presupuesto
        $name = 'presupuesto_' . $budget->reference;
        // Cifrar el nombre del archivo
        $encrypted = $this->encrypt_decrypt('encrypt', $name);
        // Ruta completa para guardar el archivo PDF
        $pathToSaveBudget = storage_path('app/public/assets/budgets/' . $encrypted . '.pdf');
        // Verificar si el directorio existe, si no, crearlo
        $directory = storage_path('app/public/assets/budgets/');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true); // Crear el directorio con permisos 0755 y true para crear subdirectorios si es necesario
        }
        // Generar el PDF usando tu método createPdf
        $sumatorio = false;
        $pdf = $this->createPdf($budget, $sumatorio);
        // Guardar el PDF en la ruta especificada
        $pdf->save($pathToSaveBudget);
        return $encrypted;
    }

    public function getBudgetsByClientId(Request $request)
    {
        $clientId = $request->input('client_id');
        $budgets = Budget::where('client_id', $clientId)->get();
        return response($budgets);
    }
    public function getBudgetsByprojectId(Request $request)
    {
        $budgets = Budget::where('project_id',$request->input('project_id'))->get();
        return response($budgets);
    }

    public function getBudgetById(Request $request)
    {
        $id = $request->input('budget_id');
        $budget = Budget::find($id);
        return response()->json($budget);
    }


    public function sendEmail(Budget $budget, Request $request){

        $file2 = [];

        foreach($request->files as $file ){
            foreach($file as $si){
                $file2 [] = $si;
            }
        }

        $filename = $this->savePDF($budget);

        $data = [
            'admin_user_id' => Auth::user()->id,
            'budget_id' => $budget->id, /**/
            'budget_reference' => $budget->reference, /**/
            'client_id' => $budget->client_id,
            'file_name' => $filename,
            'date_send' => Carbon::now()->format('Y-m-d')
        ];

        $budgetExist = BudgetSend::where('budget_id', $budget->id)->get()->first();

        if ($budgetExist){
            DB::table("budgets_sends")->where("budget_id", $budget->id)->update($data);
        }else{
            $budgetSend = BudgetSend::create($data);
            $budgetSend->save();
        }

        $mailBudget = new \stdClass();
        $mailBudget->url = 'https://crm.hawkins.es/budget/cliente/'.$filename;
        $mailBudget->gestor = Auth::user()->name." ".Auth::user()->surname;
        $mailBudget->gestorMail = Auth::user()->email;
        $mailBudget->gestorTel = '956 662 942';

        $mail = explode(",",$request->email)[0];

        $mailsCC = [];
        $mailsBCC = [];

        $mailsBCC[] = "emma@lchawkins.com";
        $mailsBCC[] = "ivan@lchawkins.com";
        $mailsBCC[] = $mailBudget->gestorMail;
        $mailsBCC[] = $budget->usuario->email ;

        if($request->cc){
            $mailsCC[] = $request->cc;
        }

        if($request->cc2){
            $mailsCC[] = $request->cc2;
        }

        $data = [];

        if (!empty($file2)) {
            $timestamp = time(); // Obtén el timestamp una sola vez para evitar múltiples llamadas a time()

            foreach ($file2 as $index => $fileNew) {
                // Genera un nombre de archivo único usando el índice y el timestamp
                $fileName = 'supplier-' . $timestamp . '-' . $index . '.' . $fileNew->getClientOriginalExtension();

                // Almacena el archivo en la ubicación deseada y guarda el path en el array de datos
                $path = Storage::putFileAs('public/assets/suppliers', $fileNew, $fileName);

                // Agrega la ruta completa al array de datos
                $data[] = storage_path('app/' . $path);
            }

            // Marca que hay archivos adjuntos
            $mailBudget->files = true;
        }

        $email = new MailBudget($mailBudget,$data);

        Mail::to($mail)
        ->bcc($mailsBCC)
        ->cc($mailsCC)
        ->send($email);

        $fechaNow = Carbon::now();

        $alertNew['admin_user_id'] = Auth::user()->id;
        $alertNew['stage_id'] = 21;  //etapa Pendiente de confirmar
        $alertNew['activation_datetime'] = $fechaNow->addDays(2)->format('Y-m-d H:i:s');
        $alertNew['status_id'] = 1; // Estado pendiente
        $alertNew['reference_id'] = $budget->id;    //Referencia del presupuesto

        $alert = Alert::create($alertNew);
        $alertSaved = $alert->save();

        $mailEmisor = "budget@crmhawkins.com";

        $logData = [
            'mailEmisor' => $mailEmisor,
            'mailReceptor' => $mail,
            'status' => 4, // Tipo de log, en el caso 4 es de los budgets, ver vista de logs_email (index) para ver los tipos que hay
            'mensaje' => "Nuestro equipo ha trabajado para ofrecerte la mejor propuesta. Espero que sea de tu agrado, recuerda que estoy disponible para ayudarte en lo que necesites",
        ];

        $logEmail = $this->getLogEmails($logData);

        return response()->json([
            'status' => true,
            'mensaje' => "El presupuesto ha sido enviado con éxito."
        ]);
    }

    public function getLogEmails($logData){

        $mailEmisor = $logData['mailEmisor'];
        $mailReceptor = $logData['mailReceptor'];
        $status = $logData['status'];
        $mensaje = $logData['mensaje'];

        $data = [
            "mail_emisor" => $mailEmisor,
            "mail_receptor" => $mailReceptor,
            "status" => $status,
            "mensaje" => $mensaje,
        ];

        $createLog = LogsEmail::create($data);
        $logSaved = $createLog->save();

        return 200;
    }

    function encrypt_decrypt($action, $string) {

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'c0c0dr1l0s3n3ln1l0';
        $secret_iv = 'c0c0dr1l0s3n3ln1l0';
        // hash
        $key = hash('sha256', $secret_key);


        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public function getBudget($budget)
    {
        $budget_val = BudgetSend::where("file_name", $budget)->get()->first();
        $filename = $budget . '.pdf';
        if ($budget_val) {
            return view('clients.budgetview', compact('budget', 'budget_val', 'filename'));
        } else {
            return view('clients.budgetnotfound');
        }
    }

    public function setAcceptance(Request $request)
    {

        $budget = BudgetSend::find($request->id);
        $clientIP = request()->ip();

        if ($budget) {
            $budget->acceptance_conds = 1;
            $budget->IP = $clientIP;
            $budget->save();

            //Creación de alerta
            $alert = [
                "admin_user_id" => $budget->admin_user_id,
                "stage_id" => 12,
                "activation_datetime" => Carbon::now()->format('Y-m-d H:i:s'),
                "status_id" => 1,
                "reference_id" => $budget->id
            ];

            $savedAlert = Alert::create($alert);
            $savedAlert->save();


            $alertdDelete = Alert::where("stage_id", 21)->where("reference_id", $budget->id)->get();

            foreach ($alertdDelete as $alert) {
                $alert->delete();
            }
        }
    }

}
