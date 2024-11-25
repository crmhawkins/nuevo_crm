<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Mail\MailInvoice;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\InvoiceCustomPDF;
use App\Models\Clients\Client;
use App\Models\Company\CompanyDetails;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\InvoiceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
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
            'observations' => 'nullable',
            'note' => 'nullable',
            'show_summary' => 'nullable',
            'creation_date' => 'nullable',
            'created_at' => 'nullable',
            'paid_date' => 'nullable'
        ]);

        // Formulario datos

        $facturaupdated=$factura->update($data);

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

            // Guardar el archivo PDF en la carpeta temporal
            $pdfFilePath = $tempDirectory . 'factura_' . $invoice->reference . '_' . Carbon::now()->format('Y-m-d') . '.pdf';
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
            return 200;
        }else{
            return 404;
        }

    }

    public function savePDF(Invoice $invoice){


        $name = 'factura_' . $invoice['reference'];
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

        $fac = new Facturae();

        $partes = explode('-', $factura->reference);

        $numero = $partes[0];
        $serie = $partes[1];
        $fac->setNumber($numero,$serie);

        // Asignamos la fecha
        $fecha = Carbon::parse($factura->created_at)->format('Y-m-d');
        $fac->setIssueDate($fecha);

        // Incluimos los datos del vendedor
        $fac->setSeller(new FacturaeParty([
            "taxNumber" => $empresa->nif,
            "name"      => $empresa->company_name,
            "address"   => $empresa->address,
            "postCode"  => $empresa->postCode,
            "town"      => $empresa->town,
            "province"  => $empresa->province
        ]));

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
            if ($concepto->discount > 0) {

                $fac->addItem(new FacturaeItem([
                    "articleCode" => $concepto->services_category_id,
                    "name" => $concepto->title,
                    "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                    "quantity" => $concepto->units,
                    "discounts" => [
                          ["reason" => "Descuento", "amount" => $concepto->discount]
                    ],
                    "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage]
                ]));
            }else {
                $fac->addItem(new FacturaeItem([
                    "articleCode" => $concepto->services_category_id,
                    "name" => $concepto->title,
                    "unitPriceWithoutTax" => $concepto->total_no_discount / $concepto->units,
                    "quantity" => $concepto->units,
                    "taxes" => [Facturae::TAX_IVA => $factura->iva_percentage]
                ]));
            }

        }

        $certificado = $empresa->certificado;
        $contrasena = $empresa->contrasena;

        if (empty($certificado)) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Falta el certificado.'
            ]);
        }
        if (empty($contrasena)) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Falta la contraseña del certificado.'
            ]);
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
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El archivo no se generó correctamente.'
            ]);
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
