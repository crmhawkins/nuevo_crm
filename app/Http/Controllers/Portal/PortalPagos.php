<?php
namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Coupon;
use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Clients\Client;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\InvoiceReferenceAutoincrement;
use App\Models\PortalCoupon;
use App\Models\PortalPurchaseDetail;
use App\Models\Projects\Project;
use App\Models\Purchase;
use App\Models\TempUser;
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
        if ($cliente->id == 320574) {
            return view('portal.temp.tempcheckout-estructura', compact('type'));
        }
        return view('portal.checkout-estructura', compact('cliente', 'type'));
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

        if ($cliente->id == 320574) {
            return view('portal.temp.tempcheckout', compact('cliente', 'type', 'price', 'purchase_type', 'iva', 'purchase_id'));
        } else {
            return view('portal.checkout', compact('cliente', 'type', 'price', 'purchase_type', 'iva', 'purchase_id'));
        }
    }

    public function processPayment(Request $request)
    {
        $cliente = session('cliente');
        if (!$cliente) return redirect()->route('portal.login');

        $isTempUser = $cliente->id == 320574;
        $tempUser = session('tempuser');
        $tempUserId = $tempUser->user;
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));


        $validator = $this->validarFormulario($request, $isTempUser);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error_message', 'Corrige los errores del formulario.')
                ->with(compact('cliente'))
                ->with(['purchase_id' => $request->input('purchase_id')]);
        }
        $purchase_id = $request->input('purchase_id');
        try {
            $purchase = Purchase::find($request->input('purchase_id'));
            if (!$purchase) {
                return redirect()->back()->with('error_message', 'Compra no encontrada.');
            }

            $baseAmount = 19000;
            [$amount, $discountAmount, $couponCode] = $this->aplicarCupon($request, $baseAmount, $cliente);

            $finalAmount = $amount + ($amount * 0.21);
            if ($finalAmount < 1) {
                $purchase->update([
                    'payment_status' => 'gratuito',
                    'status' => 'pagado',
                    'amount' => 0
                ]);
                if ($isTempUser) {
                    $this->crearClienteDesdeTempUser($tempUser, $request, $purchase_id);
                    $cliente = Client::where('id', $tempUserId)->first();
                    session(['cliente' => $cliente]);
                }
                $this->createPresupuesto($cliente, $request->input('purchase_type'), 0);
                return redirect()->route('portal.compras', compact('cliente'))
                    ->with('success_message', 'Compra gratuita completada. ¡Gracias!');
            }

            if (!$cliente->stripe_customer_id) {
                $stripeCustomer = \Stripe\Customer::create([
                    'email' => $request->input('email'),
                    'name' => $request->input('full_name'),
                    'address' => [
                        'line1' => implode(' ', [
                            $request->input('country'),
                            $request->input('province'),
                            $request->input('city'),
                            $request->input('address'),
                            $request->input('zipcode'),
                        ]),
                    ],
                ]);

            }

            $charge = \Stripe\Charge::create([
                'amount' => $finalAmount,
                'currency' => 'eur',
                'description' => ucfirst($request->input('purchase_type')) . ' personalizada',
                'receipt_email' => $request->input('email'),
                'source' => $request->stripeToken,
                'metadata' => [
                    'customer_name' => $request->input('full_name'),
                    'purchase_type' => $request->input('purchase_type'),
                    'coupon_code' => $couponCode ?? 'Sin cupón',
                    'descuento' => number_format($discountAmount / 100, 2) . ' €',
                ]
            ]);

            if ($charge->status === 'succeeded') {
                $purchase->update([
                    'payment_status' => 'succeeded',
                    'stripe_charge_id' => $charge->id,
                    'status' => 'pagado',
                    'amount' => $finalAmount / 100
                ]);

                if ($isTempUser) {
                    $this->crearClienteDesdeTempUser($tempUser, $request, $purchase_id);
                }
                $cliente = Client::where('id', $tempUserId)->first();
                session(['cliente' => $cliente]);
                $this->createPresupuesto($cliente, $request->input('purchase_type'), $amount / 100);
                return redirect()->route('portal.compras', compact('cliente'))
                    ->with('success_message', 'Pago realizado correctamente.');
            }

            return redirect()->route('portal.dashboard')->with('error_message', 'No se pudo completar el pago.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error_message', 'Error en el pago: ' . $e->getMessage());
        }
    }

    private function validarFormulario($request, $isTempUser)
    {
        $rules = [
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'nif'           => 'required|string|max:20',
            'country'       => 'required|string|max:30',
            'province'      => 'required|string|max:30',
            'city'          => 'required|string|max:30',
            'zipcode'       => 'required|string|max:30',
            'address'       => 'required|string|max:255',
            'stripeToken'   => 'required|string',
            'purchase_id'   => 'required|exists:purchases,id',
            'purchase_type' => 'required|string',
            'phone' => 'required|string|max:20'
        ];


        return Validator::make($request->all(), $rules);
    }

    private function aplicarCupon($request, $baseAmount, $cliente)
    {
        $discountAmount = 0;
        $couponCode = $request->input('coupon');

        if ($couponCode) {
            $coupon = PortalCoupon::where('id', $couponCode)->where('used', 0)->first();
            if ($coupon && $coupon->discount) {
                $discountAmount = round($baseAmount * ($coupon->discount / 100));
                $coupon->used = 1;
                $coupon->update(['used' => 1]);
            }
        }

        $amount = max($baseAmount - $discountAmount, 0);
        return [$amount, $discountAmount, $couponCode];
    }

    private function crearClienteDesdeTempUser($tempUser, $request, $purchase_id)
    {
        $nuevo = new Client([
            'id' => $tempUser->user,
            'name' => $request->input('full_name'),
            'company' => $request->input('full_name'),
            'admin_user_id' => 103,
            'email' => $request->input('email'),
            'cif' => $request->input('nif'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
            'province' => $request->input('province'),
            'zipcode' => $request->input('zipcode'),
            'phone' => $request->input('phone'),
            'is_client' => 1,
            'privacy_policy_accepted' => 1,
            'cookies_accepted' => 1,
            'newsletters_sending_accepted' => 1,
            'pin' => $tempUser->password,
        ]);

        $nuevo->save();

        $purchase = Purchase::where('id', $purchase_id)->first();
        if ($purchase) {
            $purchase->client_id = $nuevo->id;
            $purchase->save();
        }

        $tempUser->delete();
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
