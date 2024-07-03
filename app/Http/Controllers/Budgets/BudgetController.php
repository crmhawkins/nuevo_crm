<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Budgets\BudgetStatu;
use App\Models\Clients\Client;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Projects\Project;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        session('clienteId') != null ? $clienteId = session('clienteId') : $clienteId = null;

        $gestores = User::all();
        $clientes = Client::orderBy('id', 'asc')->get();
        // $campanias = Project::all();
        $formasPago = PaymentMethod::all();
        $campanias = [];

        return view('budgets.create', compact('gestores', 'clientes', 'campanias', 'formasPago', 'clienteId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'client_id' => 'required|integer',
            'project_id' => 'required|integer',
            'admin_user_id' => 'required|integer',
            'concept' => 'required|max:200',
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'project_id.required' => 'La campaña es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
        ]);

        $budgetTemporal = Budget::where('temp', true)->orderBy('created_at', 'desc')->first();
        $referenceTemp = $budgetTemporal === null ? 'temp_00' : $this->generateReferenceTemp($budgetTemporal->reference);
        // $referenceTemp = $this->generateReferenceTemp(intval('temp_00'));

        // dd($referenceTemp);
        $data = $request->all();
        $data['temp'] = true;
        $data['budget_status_id'] = 2;
        $data['reference'] = $referenceTemp;
        $data['creation_date'] = Carbon::now();

        $budgetCreado = Budget::create($data);
        return redirect(route('presupuesto.edit', $budgetCreado->id));

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $presupuesto = Budget::find($id);
        return view('budgets.show', compact('presupuesto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $presupuesto = Budget::find($id);
        $gestores = User::all();
        $campanias = Project::where('client_id', $presupuesto->client_id)->get();
        $formasPago = PaymentMethod::all();
        $estadoPresupuesto = BudgetStatu::all();
        if (!$presupuesto) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El presupuesto no existe'
            ]);
            return redirect()->route('presupuestos.index');
        }
        $budgetConcepts = BudgetConcept::where("budget_id", $id)->get();
        $thisBudgetStatus = BudgetStatu::where('id',$presupuesto->budget_status_id)->get()->first();


        return view('budgets.edit', compact('presupuesto', 'campanias', 'gestores', 'formasPago','estadoPresupuesto', 'budgetConcepts','thisBudgetStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
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
        if($thisBudgetConcepts->isEmpty()){
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'Para guardar el presupuesto debe tener al menos un concepto.'
            ]);
        }

        // Comprobar existencia a la hora de guardar por si se eliminó un registro durante la creación
        $clientID = $data['client_id'];
        $projectID = $data['project_id'];
        $adminUserID = $data['admin_user_id'];

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

        $data['discount'] = $updateBudgetQuantities['discount'];
        $data['gross'] = $updateBudgetQuantities['gross'];
        $data['base'] = $updateBudgetQuantities['base'];
        $data['total'] = $data['base'] + $data['iva'];

        if(!$data['iva']){
            $data['iva'] = $updateBudgetQuantities['iva'];
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
                    $newReference = 'delete_' . substr($budget->reference, 5); // asumiendo que el formato es 'temp_xx'
                    $budget->reference = $newReference;
                    $budget->save();
                }
    
                // Eliminar todos los conceptos relacionados con el presupuesto
                $budgetConcepts = BudgetConcept::where('budget_id', $budget->id)->get();
                if (count($budgetConcepts) > 0) {
                    foreach ($budgetConcepts as $budgetConcept) {
                        $budgetConcept->delete();
                    }
                } 
                // else {
                //     return response()->json([
                //         'status' => false,
                //         'mensaje' => "Error 500 no se borraron los conceptos."
                //     ]);
                // }
    
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
        $referencia = $this->generateBudgetReference($budget);
        // dd($referencia);
        if ($referencia === null) {
            return response(500);
        }
        $budget->temp = 0;
        $budget->reference = $referencia['reference'];
        $budget->reference_autoincrement_id = $referencia['id'];
        $budget->budget_status_id = 3;
        $budget->save();
        return response(200);
        // session()->flash('toast', [
        //     'icon' => 'success',
        //     'mensaje' => 'El presupuesto cambio su estado a Aceptado'
        // ]);
        // return redirect(route('presupuesto.edit', $id));
    }


    private function generateReferenceTemp($numero){
        // Incrementa el número primero
        $incrementedNumber = intval($numero) + 1;
        // Asegura que el número tenga dos dígitos
        $formattedNumber = str_pad($incrementedNumber, 2, '0', STR_PAD_LEFT);
        // Concatena con la cadena "temp_"
        return "temp_" . $formattedNumber;
    }

    public function generateBudgetReference(Budget $budget) {
        // Obtener la fecha actual del presupuesto
        $budgetCreationDate = $budget->creation_date ?? now();
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
        if($conceptsDiscounts){
            foreach($conceptsDiscounts as $key => $value){

                // Guardar el descuento del concepto y recalcular el total
                // Encuentro el concepto
                $budgetConceptsUdpdateDiscount = BudgetConcept::where('id', $key )->get()->first();

                if($budgetConceptsUdpdateDiscount->concept_type_id == BudgetConceptType::TYPE_SUPPLIER){
                     // Calculo el total
                     $totalNoDiscount = $budgetConceptsUdpdateDiscount->total_no_discount;
                     $discount = $value;
                     $discountToSubstract = ($totalNoDiscount*$discount)/100;
                     $newTotal = $totalNoDiscount - $discountToSubstract;
                     $newTotalFormated  =  $newTotal*100/100;
                     // Actualizo el concepto con el total y el descuento
                     $budgetConceptUdpdateDiscount = BudgetConcept::where('id', $key )->update(array('discount' =>   $discount, 'total' => $newTotalFormated  ));

                }
                if($budgetConceptsUdpdateDiscount->concept_type_id == BudgetConceptType::TYPE_OWN){
                    // Calculo el total
                    $totalNoDiscount = $budgetConceptsUdpdateDiscount->total_no_discount;
                    $discount = $value;
                    $discountToSubstract = ($totalNoDiscount*$discount)/100;
                    $newTotal = $totalNoDiscount - $discountToSubstract;
                    $newTotalFormated  =  $newTotal*100/100;
                    // Actualizo el concepto con el total y el descuento
                    $budgetConceptUdpdateDiscount = BudgetConcept::where('id', $key )->update(array('discount' =>   $discount, 'total' => $newTotalFormated  ));
                }
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
            $grossFormated = 0;
            $base = 0;
            $baseFormated = 0;
            $discountQuantity = 0;
            $discountQuantityFormated = 0;
            $iva = 0;
            $ivaFormated =  0;
            $total = 0;
            $totalFormated = 0;
            // Recorro todos los conceptos y voy realizando los calculos
            foreach($thisBudgetConcepts as $concept){
                // Proveedor
                if($concept->concept_type_id == BudgetConceptType::TYPE_SUPPLIER){
                    $units = $concept->units;
                    $purchasePriceWithoutMarginBenefit = $concept->purchase_price;
                    $benefitMargin = $concept->benefit_margin;
                    $marginBenefitToAdd  =  ($purchasePriceWithoutMarginBenefit*$benefitMargin)/100;
                    $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit+ $marginBenefitToAdd;
                    $gross += $purchasePriceWithMarginBenefit;
                    $grossFormated =  $gross*100/100;
                    $base += $concept->total ;
                    $baseFormated = $base*100/100;
                    $salePrice =  $concept->sale_price;
                    $discountPercentage = $concept->discount;
                    if($discountPercentage > 0){
                        $discountQuantity += (($purchasePriceWithMarginBenefit)*$discountPercentage)/100 ;
                        $discountOfThisConcept =  (($purchasePriceWithMarginBenefit)*$discountPercentage)/100 ;
                        $discountQuantityFormated = $discountQuantity*100/100;
                    }else{
                        $discountQuantityFormated += 0;
                    }
                    $iva += ($concept->total * $ivaPercentage) / 100;
                    $ivaFormated =  $iva*100/100;
                }
                // Propio
                if($concept->concept_type_id == BudgetConceptType::TYPE_OWN){
                    $units = $concept->units;
                    $cont=0;
                    $gross += $concept->total_no_discount;
                    $grossFormated =  $gross*100/100;
                    $base += $concept->total;
                    $salePrice =  $concept->sale_price;
                    $discountPercentage = $concept->discount;
                    if($discountPercentage > 0){
                        $discountQuantity += ($concept->total_no_discount*$discountPercentage)/100 ;
                        $discountOfThisConcept =  (($salePrice)*$discountPercentage)/100 ;
                        $discountQuantityFormated = $discountQuantity*100/100;
                    }else{
                        $discountQuantityFormated += 0;
                    }
                    // Calculo el IVA
                    $iva += ($concept->total * $ivaPercentage) / 100;
                    $ivaFormated =  $iva*100/100;
                    //base //menos descuento
                    $baseFormated = $base*100/100;
                }
            }
            // Calculo el total
            $total += $ivaFormated + $baseFormated;
            $totalFormated = $total*100/100;
            $totalFormated =  number_format((float)$totalFormated, 2, '.', '');

            // $newBudgetQuantitiesToUpdateArray = array();
            $newBudgetQuantitiesToUpdateArray = [
                'gross' => $grossFormated,
                'base' => $baseFormated,
                'discount' => $discountQuantityFormated,
                'iva' => $ivaFormated,
                'totalFormated' => $totalFormated,
            ];
        return $newBudgetQuantitiesToUpdateArray;
        }
    }

}
