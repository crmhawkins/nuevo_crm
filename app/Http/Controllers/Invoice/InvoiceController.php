<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Budgets\BudgetController;
use App\Http\Controllers\Controller;
use App\Mail\MailInvoice;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetStatu;
use App\Models\Budgets\InvoiceCustomPDF;
use App\Models\Clients\Client;
use App\Models\Company\CompanyDetails;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\InvoiceStatus;
use App\Models\Tasks\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use josemmo\Facturae\Facturae;
use josemmo\Facturae\FacturaeItem;
use josemmo\Facturae\FacturaeParty;
use ZipArchive;

class InvoiceController extends Controller
{
    public function index()
    {
        $facturas = Invoice::all();
        return view('invoices.index', compact('facturas'));
    }
    public function edit(string $id)
    {
        $factura = Invoice::where('id', $id)->get()->first();
        $invoiceStatuses = InvoiceStatus::all();
        $invoice_concepts = InvoiceConcepts::where('invoice_id', $factura->id)->get();

        return view('invoices.edit', compact( 'factura', 'invoiceStatuses', 'invoice_concepts'));
    }

    public function cobrarFactura(Request $request)
    {
        $id = $request->id;
        $invoice = Invoice::find($id);

        $invoice->invoice_status_id = 3;
        if($invoice->budget->budget_status_id == BudgetStatu::ESPERANDO_PAGO_PARCIAL){
            $invoice->budget->budget_status_id = BudgetStatu::ACCEPTED;
            $this->createTask($invoice->budget->id);
        }
        $invoice->save();
        return response(200);
        // session()->flash('toast', [
        //     'icon' => 'success',
        //     'mensaje' => 'El presupuesto cambio su estado a Aceptado'
        // ]);
        // return redirect(route('presupuesto.edit', $id));
    }

    public function update(Request $request, string $id)
    {
        $factura = Invoice::find($id);
        // Validación

        $data = $request->validate([
            'invoice_status_id' => 'required',
            'concept' => 'required',
            'observations' => 'nullable',
            'note' => 'nullable',
            'show_summary' => 'nullable',
            'creation_date' => 'nullable',
            'created_at' => 'nullable',
            'paid_date' => 'nullable'
        ]);

        // Formulario datos

        $facturaupdated=$factura->update($data);

        if($factura->budget->budget_status_id == BudgetStatu::ESPERANDO_PAGO_PARCIAL && $factura->invoice_status_id == 3){
            $factura->budget->budget_status_id = BudgetStatu::ACCEPTED;
            $this->createTask($factura->budget->id);
        }
        if($facturaupdated){
            return redirect()->route('facturas.index')->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Presupuesto actualizado correctamente.'
            ]);
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Error al actualizar el presupuesto.'
            ]);
        }
    }

    public function createTask($id){
        $taskSaved = false;
        $budget = Budget::find($id);
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
            // Alert::create([

            // ]);
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

    public function generatePDF(Request $request)
    {
        // Buscar la factura por ID
        $invoice = Invoice::find($request->id);

        // Validar que la factura exista
        if (!$invoice) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }

        $pdf =  $this->createPdf($invoice);
        // Descargar el PDF con el nombre 'factura_XYZ_fecha.pdf'
        return $pdf->download('factura_' . $invoice->reference . '_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function createPdf(invoice $invoice){

        // Obtener los conceptos de esta factura
        $thisInvoiceConcepts = InvoiceConcepts::where('invoice_id', $invoice->id)->get();
        // Título del PDF
        $title = "Factura - " . $invoice->reference;
        // Datos básicos para pasar a la vista del PDF
        $data = [
            'title' => $title,
            'invoice_reference' => $invoice->reference,
        ];
        // Formatear los conceptos para usarlos en la vista
        $invoiceConceptsFormated = [];

        foreach ($thisInvoiceConcepts as $invoiceConcept) {
            // Validar que tenga unidades mayores a 0 para evitar división por 0
            if ($invoiceConcept->units > 0) {
                // Título
                $invoiceConceptsFormated[$invoiceConcept->id]['title'] = $invoiceConcept->title ?? 'Título no disponible';
                // Unidades
                $invoiceConceptsFormated[$invoiceConcept->id]['units'] = $invoiceConcept->units;

                // Precio por unidad
                $invoiceConceptsFormated[$invoiceConcept->id]['price_unit'] = round($invoiceConcept->total / $invoiceConcept->units, 2);

                // Calcular subtotal y precio en función del tipo de concepto
                if ($invoiceConcept->concept_type_id == BudgetConceptType::TYPE_OWN) {
                    $invoiceConceptsFormated[$invoiceConcept->id]['subtotal'] = number_format((float)$invoiceConcept->units * $invoiceConcept->sale_price, 2, '.', '');
                    $invoiceConceptsFormated[$invoiceConcept->id]['price_unit'] = number_format((float)$invoiceConcept->sale_price, 2, '.', '');
                } elseif ($invoiceConcept->concept_type_id == BudgetConceptType::TYPE_SUPPLIER) {
                    $purchasePriceWithoutMarginBenefit = $invoiceConcept->purchase_price;
                    $benefitMargin = $invoiceConcept->benefit_margin;
                    $marginBenefitToAdd  = ($purchasePriceWithoutMarginBenefit * $benefitMargin) / 100;
                    $purchasePriceWithMarginBenefit  = $purchasePriceWithoutMarginBenefit + $marginBenefitToAdd;
                    $invoiceConceptsFormated[$invoiceConcept->id]['price_unit'] = round($purchasePriceWithMarginBenefit / $invoiceConcept->units, 2);
                    $invoiceConceptsFormated[$invoiceConcept->id]['subtotal'] = number_format((float)$invoiceConcept->total_no_discount, 2, '.', '');
                }
                // Descuento
                $invoiceConceptsFormated[$invoiceConcept->id]['discount'] = number_format((float)($invoiceConcept->discount ?? 0), 2, ',', '');
                // Total
                $invoiceConceptsFormated[$invoiceConcept->id]['total'] = number_format((float)$invoiceConcept->total, 2, ',', '');
                // Formatear la descripción dividiendo en líneas
                $rawConcepts = $invoiceConcept->concept ?? '';
                $arrayConceptStringsAndBreakLines = explode(PHP_EOL, $rawConcepts);

                $maxLineLength = 50;
                $charactersInALineCounter = 0;
                $arrayWordsFormated = [];
                $counter = 0;
                $firstWordTempRow = true;

                foreach ($arrayConceptStringsAndBreakLines as $stringItem) {
                    $rowWords = explode(' ', $stringItem);
                    $tempRow = '';

                    foreach ($rowWords as $word) {
                        $wordLength = strlen($word);

                        if (!$firstWordTempRow && ($charactersInALineCounter + $wordLength) > $maxLineLength) {
                            // Guardar la fila actual y reiniciar el contador
                            $arrayWordsFormated[$counter] = trim($tempRow);
                            $counter++;
                            $tempRow = $word;
                            $charactersInALineCounter = $wordLength;
                        } else {
                            $tempRow .= ($firstWordTempRow ? '' : ' ') . $word;
                            $charactersInALineCounter += $wordLength;
                            $firstWordTempRow = false;
                        }
                    }

                    // Guardar la última fila
                    $arrayWordsFormated[$counter] = trim($tempRow);
                    $counter++;
                    $charactersInALineCounter = 0;
                    $firstWordTempRow = true;
                }

                $invoiceConceptsFormated[$invoiceConcept->id]['description'] = $arrayWordsFormated;
            } else {

                // Manejar casos donde las unidades sean 0 o nulas
                $invoiceConceptsFormated[$invoiceConcept->id] = [
                    'title' => $invoiceConcept->title ?? 'Título no disponible',
                    'units' => 0,
                    'price_unit' => 0,
                    'subtotal' => 0,
                    'discount' => '0,00',
                    'total' => '0,00',
                    'description' => ['Descripción no disponible']
                ];
            }
        }

        // Generar el PDF usando la vista 'invoices.previewPDF'
        $pdf = PDF::loadView('invoices.previewPDF', compact('invoice','data', 'invoiceConceptsFormated'));
        return $pdf;
    }

    public function generateMultiplePDFs(Request $request)
    {
        // Obtener las facturas por sus IDs
        $invoices = Invoice::whereIn('id', $request->invoice_ids)->get();

        // Verificar que se encontraron facturas
        if ($invoices->isEmpty()) {
            return response()->json(['error' => 'No se encontraron facturas'], 404);
        }

        // Crear una carpeta temporal para almacenar los archivos PDF
        $tempDirectory = storage_path('app/public/temp/invoices/');
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        // Almacenar los nombres de los archivos PDF generados
        $pdfFiles = [];

        foreach ($invoices as $invoice) {

            $pdf = $this->createPDF($invoice);
            $nombre = $invoice->reference;
            $nombre = str_replace('/', '_', $nombre);
            // Guardar el archivo PDF en la carpeta temporal
            $pdfFilePath = $tempDirectory . 'factura_' . $nombre . '_' . Carbon::now()->format('Y-m-d') . '.pdf';
            $pdf->save($pdfFilePath);

            // Añadir el archivo generado al array
            $pdfFiles[] = $pdfFilePath;
        }

        // Crear un archivo ZIP que contendrá todos los PDFs
        $zipFileName = 'facturas_' . Carbon::now()->format('Y-m-d') . '.zip';
        $zipFilePath = storage_path('app/public/temp/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Agregar cada archivo PDF al ZIP
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Eliminar los archivos PDF individuales después de crear el ZIP
        foreach ($pdfFiles as $file) {
            unlink($file);
        }

        // Descargar el archivo ZIP
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }



    public function rectificateInvoice(Request $request){
        $invoice = Invoice::find($request->id);
        // Si es rectificativa que de error
        if($invoice->rectification){
            return response()->json([
                'status' => false,
                'mensaje' => "La factura ya es rectificativa"
            ]);
        }
        $arrayUpdated = ['budget_status_id' => 4];
        $budget = Budget::where('id', $invoice->budget_id)->get()->first();
        $budget->budget_status_id = 4;
        $budget->save();
        $rectificationSuccess = Invoice::where('id', $invoice->id )->update(array(
            'invoice_status_id' =>  5, //cancelada
            'rectification' =>  1,
        ));


        // Actualizar a rectificada
        $rectificationSuccess = Invoice::where('id', $invoice->id )->get()->first();
        $new_factura = $rectificationSuccess->replicate();
        $new_factura->total = -$new_factura->total;
        $new_factura->gross = -$new_factura->gross;
        $new_factura->base = -$new_factura->base;
        $new_factura->reference = 'N' . $invoice->reference;
        $new_factura->update(array(
            'invoice_status_id' =>  5, //cancelada
            'rectification' =>  1,
        ));
        $new_factura->push();

        $conceptos = InvoiceConcepts::where('invoice_id', $invoice->id)->get();

        foreach ($conceptos as $concept) {
            $new_concept = $concept->replicate();
            $new_concept->invoice_id = $new_factura->id;
            $new_concept->total = -$new_concept->total;
            $new_concept->push();
        }

        // Actualizar presupuesto a cancelado tras rectificar


        // Respuesta
        if($new_factura){
            return response()->json([
                'status' => true,
                'mensaje' => "Factura marcada como rectificativa.",
                'id' => $new_factura->id

            ]);

        }else{
            return response()->json([
                'status' => false,
                'mensaje' => "Error al actualizar datos."
            ]);
        }

    }

    public function destroy(Request $request){
        $id = $request->id;
        if ($id != null) {
            $invoice = Invoice::find($id);
            if ($invoice != null) {
                // Eliminar el presupuesto
                $invoice->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El factura fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra la factura."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function sendInvoicePDF(Request $request)
    {
        $invoice = Invoice::where('id', $request->id)->get()->first();

        $filename = $this->savePDF($invoice);

        $data = [
            'file_name' => $filename
        ];

        $mailInvoice = new \stdClass();
        $mailInvoice->gestor = $invoice->adminUser->name." ".$invoice->adminUser->surname;
        $mailInvoice->gestorMail = $invoice->adminUser->email;
        $mailInvoice->gestorTel = '956 662 942';
        $mailInvoice->paymentMethodId = $invoice->paymentMethod->id;

        $email = new MailInvoice($mailInvoice, $filename);

        Mail::to($request->email)
        ->cc('administracion@lchawkins.com')
        ->bcc('ivan@lchawkins.com')
        ->send($email);

        // Respuesta
        if(File::delete($filename)){
            // Respuesta
            return response()->json([
                'status' => true,
                'mensaje' => "Factura enviada correctamente."
            ]);
        }else{
            return response()->json([
                'status' => false,
                'mensaje' => "Error al enviar la factura."
            ]);
        }

    }

    public function savePDF(Invoice $invoice){


        $name = 'factura_' . str_replace('/','-',$invoice['reference']);
        $pathToSaveInvoice =  storage_path('app/public/assets/temp/' . $name . '.pdf');
        $directory = storage_path('app/public/assets/temp');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true); // Crear el directorio con permisos 0755 y true para crear subdirectorios si es necesario
        }
        $pdf = $this->createPdf($invoice);
        $pdf->save( $pathToSaveInvoice );
        return $pathToSaveInvoice;

    }

    public function electronica(Request $request)
    {
        $factura = Invoice::find($request->id);
        $empresa = CompanyDetails::first();
        $cliente = Client::where('id', $factura->client_id)->first();
        $conceptos = InvoiceConcepts::where('invoice_id', $factura->id)->get();
        $kitdigital  = false;
        $kitconsulting  = false;

        $facturaconcepts = explode('/', $factura->concept);
        $kit = $facturaconcepts[0];

        if($kit == 'KD'){
            $kitdigital = true;
        }else if($kit == 'KC'){
            $kitconsulting = true;
        }

        $fac = new Facturae();

        $partes = explode('-', $factura->reference);
        $numero = $partes[0];
        $numero = str_replace('/', '', $numero);
        $serie = $partes[1];
        $fac->setNumber($numero,$serie);

        // Asignamos la fecha
        $fecha = Carbon::parse($factura->created_at)->format('Y-m-d');
        $fac->setIssueDate($fecha);

        $inicio = $request->input('periodo_inicio');
        $fin    = $request->input('periodo_fin');

        // Si no se proporcionan fechas, se usa la lógica actual
        if (!$inicio || !$fin) {
            $inicio = $fecha;
            $fin = Carbon::parse($factura->created_at)->addyears(1)->format('Y-m-d');
        }

        $fac->setBillingPeriod($inicio, $fin);

        $isceuta = $factura->is_ceuta == 1 ? true : false;

        // Incluimos los datos del vendedor
        if($isceuta){
            $fac->setSeller(new FacturaeParty([
                "taxNumber" => $empresa->nif,
                "name"      => $empresa->company_name,
                "address"   => "Calle Delgado Serrano Nº1, 3ºD, 1ªOficina",
                "postCode"  => "51001",
                "town"      => "CEUTA",
                "province"  => "CEUTA"
            ]));

        }else{
            $fac->setSeller(new FacturaeParty([
                "taxNumber" => $empresa->nif,
                "name"      => $empresa->company_name,
                "address"   => $empresa->address,
                "postCode"  => $empresa->postCode,
                "town"      => $empresa->town,
                "province"  => $empresa->province
            ]));
        }

        if ($cliente->tipoCliente == 1) {
            $camposRequeridos = [
                'CIF' => $cliente->cif,
                'Nombre' => $cliente->name,
                'Primer Apellido' => $cliente->primerApellido,
                'Segundo Apellido' => $cliente->segundoApellido,
                'Dirección' => $cliente->address,
                'Código Postal' => $cliente->zipcode,
                'Ciudad' => $cliente->city,
                'Provincia' => $cliente->province
            ];
        } else {
            $camposRequeridos = [
                'CIF' => $cliente->cif,
                'Nombre de la Empresa' => $cliente->company,
                'Dirección' => $cliente->address,
                'Código Postal' => $cliente->zipcode,
                'Ciudad' => $cliente->city,
                'Provincia' => $cliente->province
            ];
        }

        // Verificar si hay algún campo vacío
        $camposFaltantes = [];
        foreach ($camposRequeridos as $campo => $valor) {
            if (empty($valor)) {
                $camposFaltantes[] = $campo;
            }
        }
        if (!empty($camposFaltantes)) {
            $mensaje = "Por favor, rellena los siguientes campos: " . implode(", ", $camposFaltantes);

            return response()->json(['error' => $mensaje, 'status' => false]);

        }


        if($cliente->tipoCliente == 1){

            $fac->setBuyer(new FacturaeParty([
                "isLegalEntity" => false,       // Importante!
                "taxNumber"     => $cliente->cif,
                "name"          => $cliente->name,
                "firstSurname"  => $cliente->primerApellido,
                "lastSurname"   => $cliente->segundoApellido,
                "address"       => $cliente->address,
                "postCode"      => $cliente->zipcode,
                "town"          => $cliente->city,
                "province"      => $cliente->province
            ]));
        }else {

            $fac->setBuyer(new FacturaeParty([
                "isLegalEntity" => true,       // Importante!
                "taxNumber"     => $cliente->cif,
                "name"          => $cliente->company,
                "address"       => $cliente->address,
                "postCode"      => $cliente->zipcode,
                "town"          => $cliente->city,
                "province"      => $cliente->province,
            ]));
        }


        foreach ($conceptos as $key => $concepto) {
            if($kitdigital){
                if($factura->is_ceuta){
                    $item = new FacturaeItem([
                        "name" => $factura->concept.' '.$factura->project->name ,
                        "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                        "quantity" => $concepto->units,
                        "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage],
                        "specialTaxableEventCode" => $factura->iva_percentage == 0 ? FacturaeItem::SPECIAL_TAXABLE_EVENT_EXEMPT : null,
                        "specialTaxableEventReason" => $factura->iva_percentage == 0 ? "Operación con inversión del sujeto pasivo conforme al Art 84 (UNO.2º) de la Ley del IVA 37/1992" : null,
                    ]);
                }else{
                    $item = new FacturaeItem([
                        "name" => $factura->concept.' '.$factura->project->name ,
                        "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                        "quantity" => $concepto->units,
                        "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage],
                        "specialTaxableEventCode" => $factura->iva_percentage == 0 ? FacturaeItem::SPECIAL_TAXABLE_EVENT_EXEMPT : null,
                        "specialTaxableEventReason" => $factura->iva_percentage == 0 ? "Inversión del sujeto pasivo conforme al art. 84.1.2º de la Ley 37/1992, del IVA." : null,
                    ]);
                }
            }else{
                if($factura->is_ceuta){
                    if ($concepto->discount > 0) {
                       $item =  new FacturaeItem([
                            "name" => $concepto->title,
                            "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                            "quantity" => $concepto->units,
                            "discounts" => [
                                  ["reason" => "Descuento", "amount" => $concepto->discount]
                            ],
                            "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage],
                            "specialTaxableEventCode" => $factura->iva_percentage == 0 ? FacturaeItem::SPECIAL_TAXABLE_EVENT_EXEMPT : null,
                            "specialTaxableEventReason" => $factura->iva_percentage == 0 ? "Operación con inversión del sujeto pasivo conforme al Art 84 (UNO.2º) de la Ley del IVA 37/1992" : null,
                        ]);
                    }else {
                        $item = new FacturaeItem([
                            "name" => $concepto->title,
                            "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                            "quantity" => $concepto->units,
                            "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage],
                            "specialTaxableEventCode" => $factura->iva_percentage == 0 ? FacturaeItem::SPECIAL_TAXABLE_EVENT_EXEMPT : null,
                            "specialTaxableEventReason" => $factura->iva_percentage == 0 ? "Operación con inversión del sujeto pasivo conforme al Art 84 (UNO.2º) de la Ley del IVA 37/1992" : null,
                        ]);
                    }
                }else{
                    if ($concepto->discount > 0) {
                       $item =  new FacturaeItem([
                            "name" => $concepto->title,
                            "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                            "quantity" => $concepto->units,
                            "discounts" => [
                                  ["reason" => "Descuento", "amount" => $concepto->discount]
                            ],
                            "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage],
                            "specialTaxableEventCode" => $factura->iva_percentage == 0 ? FacturaeItem::SPECIAL_TAXABLE_EVENT_EXEMPT : null,
                            "specialTaxableEventReason" => $factura->iva_percentage == 0 ? "Inversión del sujeto pasivo conforme al art. 84.1.2º de la Ley 37/1992, del IVA." : null,
                        ]);
                    }else {
                        $item = new FacturaeItem([
                            "name" => $concepto->title,
                            "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                            "quantity" => $concepto->units,
                            "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage],
                            "specialTaxableEventCode" => $factura->iva_percentage == 0 ? FacturaeItem::SPECIAL_TAXABLE_EVENT_EXEMPT : null,
                            "specialTaxableEventReason" => $factura->iva_percentage == 0 ? "Inversión del sujeto pasivo conforme al art. 84.1.2º de la Ley 37/1992, del IVA." : null,
                        ]);
                    }
                }
            }

            $fac->addItem($item);
        }

        if ($factura->iva_percentage == 0) {
            if($factura->is_ceuta){
                $fac->addLegalLiteral("Operación con inversión del sujeto pasivo conforme al Art 84 (UNO.2º) de la Ley del IVA 37/1992");
            }else{
                $fac->addLegalLiteral("Inversión del sujeto pasivo conforme al art. 84.1.2º de la Ley 37/1992, del IVA.");
            }
        }

        if($kitdigital){
            $fac->addLegalLiteral("Financiado por el Programa Kit Digital. Plan de Recuperación, Transformación y Resiliencia de EspañaNext Generation EU. IMPORTE SUBVENCIONADO: " . number_format($factura->base, 2) . "€");
        }

        if($kitconsulting){
            $fac->addLegalLiteral('Financiado por el Programa Kit Consulting. Plan de
            Recuperación, Transformación y Resiliencia de
            España "Next Generation EU" IMPORTE
            SUBVENCIONADO 6000€');
        }

        $certificado = $empresa->certificado;
        $contrasena = $empresa->contrasena;

        if (empty($certificado)) {
            return response()->json(['error' => 'Falta el certificado.', 'status' => false]);

        }
        if (empty($contrasena)) {
            return response()->json(['error' => 'Falta la contraseña del certificado.', 'status' => false]);

        }


        $encryptedStore = file_get_contents(asset('storage/'.$certificado));
        $fac->sign($encryptedStore, null, $contrasena);

        $fac->export($numero.'-'.$serie.".xsig");

        $filePath = public_path($numero.'-'.$serie.".xsig");

        if (file_exists($filePath)) {
            return response()->download($filePath, "$numero-$serie.xsig", [
                'Content-Type' => 'application/xsig',
                'Content-Disposition' => 'attachment; filename="' . $numero . '-' . $serie . '.xsig"',
            ])->deleteFileAfterSend(true); // Borra el archivo después de enviarlo
        } else {
            return response()->json(['error' => 'El archivo no se generó correctamente.', 'status' => false]);

        }

    }


    public function show(string $id)
    {
        $invoice = invoice::find($id);
        $empresa = CompanyDetails::find(1);
        $invoiceConcepts = InvoiceConcepts::where('invoice_id', $invoice->id)->get();


        return view('invoices.show', compact('invoice','empresa','invoiceConcepts'));
    }

}
