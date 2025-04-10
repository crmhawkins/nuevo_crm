<?php
namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\InvoiceReferenceAutoincrement;
use App\Models\PortalPurchaseDetail;
use App\Models\Projects\Project;
use App\Models\Purchase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PortalPagos extends Controller
{
    public function selectStructure($type) {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }

        $price = 190;
        $iva = $price * 0.21;
        $price = $price + $iva;        
        switch ($type) {
            case 'web':
                return view('portal.checkout-estructura', compact('cliente', 'type'));

            case 'eccommerce':
                return view('portal.checkout-estructura', compact('cliente', 'type'));

            default:
                return redirect()->route('portal.dashboard')->with('error_message', 'Hubo un problema al procesar tu solicitud.');
        }
    }

    public function checkout() {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }

        $purchase = session('purchase');
        $purchaseDetail = session('purchaseDetail');
        $type = $purchase->purchase_type;
        $purchase_type = $purchase->purchase_type;
        $price = $purchase->amount;
        $purchase_id = $purchase->id;

        $iva = $price / 1.21;
        return view('portal.checkout', compact('cliente', 'type', 'price', 'purchase_type', 'iva', 'purchase_id'));
    }

    public function processPayment(Request $request)
    {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }

        $rules = [
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'address'     => 'required|string|max:255',
            'stripeToken' => 'required|string',
            'purchase_id' => 'required|exists:purchases,id', 
            'purchase_type' => 'required|string',
        ];

        // Ejecutar validador
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('portal.dashboard', compact('cliente'))
                ->withErrors($validator)
                ->with('error_message', 'Por favor, corrige los errores del formulario.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $purchaseId = $request->input('purchase_id'); 
            $purchase = Purchase::find($purchaseId);

            if (!$purchase) {
                return redirect()->route('portal.dashboard')->with('error_message', 'Compra no encontrada.');
            }

            $purchaseType = $request->input('purchase_type');
            $amount = 19000; 
            $amountIva = $amount / 100 * 21;
            $finalAmount = $amount + $amountIva;

            $customer = \Stripe\Customer::create([
                'email' => $request->input('email'),
                'name' => $request->input('full_name'),
                'address' => [
                    'line1' => $request->input('address'),
                ],
            ]);

            $charge = \Stripe\Charge::create([
                'amount'      => $finalAmount,
                'currency'    => 'eur',
                'description' => ucfirst($purchaseType) . ' personalizada',
                'receipt_email' => $request->input('email'),
                'source'      => $request->stripeToken, 
                'metadata'    => [
                    'customer_name'    => $request->input('full_name'),
                    'customer_email'   => $request->input('email'),
                    'customer_address' => $request->input('address'),
                    'purchase_type'    => $purchaseType,
                ],
            ]);

            // Comprobar si el pago fue exitoso
            if ($charge->status === 'succeeded') {

                $purchase->update([
                    'payment_status'   => 'succeeded',
                    'stripe_charge_id' => $charge->id,
                    'status'           => 'pagado',
                    'amount'           => $finalAmount / 100,
                ]);

                // // Enviar post (pendiente)
                // $purchase_detail = PortalPurchaseDetail::find($purchaseId);
                // if ($purchase_detail->dominio) {
                //     $dominio = $purchase_detail->dominio;
                // } else {
                //     $dominio = $purchase_detail->dominio_externo;
                // }

                // $estructura = $purchase->template;

                // $postdata = [
                //     "domain"=> $dominio,
                //     "template"=> $estructura
                // ];
                // $response = Http::post('https://conversacioneshera.hawkins.es/api/subdomain_ionos.php', $postdata);

                // Crear presupuesto y facutura
                $this->createPresupuesto($cliente, $purchaseType, $amount/100);
                

                return redirect()->route('portal.compras', compact('cliente'))->with('success_message', 'Pago realizado correctamente!');
            } else {
                return redirect()->route('portal.dashboard')->with('error_message', 'La compra no se pudo completar.');
            }
        } catch (\Exception $e) {
            return redirect()->route('portal.dashboard')->with('error_message', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }



    //presupessto y factura
    public function createPresupuesto($cliente, $tipoCompra, $precio)
    {
        if($tipoCompra == 'web'){
            $name = 'B2B Web';
        }else {
            $name = 'B2B Eccommerce';
        }

        $proyecto = $this->createProyect($cliente, $name);

        if($proyecto['status'] == false){
            return 'Error en generar proyecto';
        }

        $referencia = $this->generateBudgetReference();
        $iva = $precio * 0.21;
        $dataCreate = [
            'client_id' => $cliente->id,
            'project_id' => $proyecto['id'],
            'admin_user_id' => $cliente->admin_user_id,
            'concept' => $name,
            'payment_method_id' => 1,
            'budget_status_id' => 5,
            'creation_date' => Carbon::now(),
            'discount' => 0,
            'gross' => $precio,
            'base' => $precio,
            'iva' => $iva,
            'total' => $precio + $iva,
            'iva_percentage' => 21,
            'reference' => $referencia['reference'],
            'reference_autoincrement_id' => $referencia['id'],
            'temp' => 0
        ];

        $budgetCreado = Budget::create($dataCreate);
        if($budgetCreado){
            $dataConcept = [
                'id' => $budgetCreado->id,
                'services_category_id' => 1,
                'service_id' => 1,
                'title' => $name,
                'concept' => $name,
                'units' => 1,
                'sale_price' => $precio,
                'total' => $precio,
                'concept_type_id' => 2
            ];
            // Creamos el concepto
            $conceptoCreate = BudgetConcept::create($dataConcept);
            $invoiceGenerate = $this->generateInvoice($budgetCreado);
        }
        return redirect(route('presupuesto.edit', $budgetCreado->id));

    }

    public function createProyect($cliente, $name)
    {
        // Validamos los campos
        $data = [
            'client_id' => $cliente->id,
            'name' => $name,
            'admin_user_id' => $cliente->admin_user_id
        ];

        
        $proyectoCreado = Project::create($data);
        if ($proyectoCreado){
            return ['status'=> true, 'id' => $proyectoCreado->id];
        }else {
            return ['status'=> false, 'id' => null];
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

    public function generateInvoice($budget){
        try {
        $grossfacturado=0;
        $basefacturada=0;
        $ivaTotalfacturado=0;
        $totalfacturado=0;
        $porcentaje = 21;
        $referenceGenerationResult = $this->generateInvoiceReference($budget);

        $data = [
            'budget_id' => $budget->id,
            'reference' => $referenceGenerationResult['reference'],
            'reference_autoincrement_id' => $referenceGenerationResult['id'],
            'admin_user_id' => $budget->admin_user_id,
            'client_id' => $budget->client_id,
            'project_id' => $budget->project_id,
            'payment_method_id' => $budget->payment_method_id,
            'invoice_status_id' => 3,
            'concept' => $budget->concept,
            'gross' => $budget->gross,
            'base' => $budget->base ,
            'iva' => $budget->iva,
            'iva_percentage' => $budget->iva_percentage,
            'discount' => 0,
            'discount_percentage' => 0,
            'total' => $budget->total,
        ];


        // Creación de la factura
        $invoice = Invoice::create($data);

        if(isset($invoice)){

            $budget->budget_status_id = 6;
            $budget->save();

            //////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////         CONCEPTOS PROPIOS         ///////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            $budgetHasOwnTypeConcepts = $this->budgetHasOwnTypeConcepts($budget);

            if($budgetHasOwnTypeConcepts){
                // Obtener los conceptos propios de presupuesto
                $thisBudgetOwnTypeConcepts = BudgetConcept::where('budget_id', operator: $budget->id)
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
                        'sale_price' => $thisBudgetOwnTypeConcept->sale_price * $porcentaje,
                        'total' => $thisBudgetOwnTypeConcept->total * $porcentaje,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ];

                    $conceptOwnCreate = InvoiceConcepts::create($conceptOwnData);
                    $conceptOwnSaved = $conceptOwnCreate->save();

                    if ($conceptOwnSaved) {
                        
                    }
                }
            }
        }
        } catch (Throwable $e)  {
            dd($e);
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
}
