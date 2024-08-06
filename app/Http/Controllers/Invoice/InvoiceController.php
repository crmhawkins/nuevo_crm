<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\InvoiceCustomPDF;
use App\Models\Clients\Client;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\InvoiceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


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
        $invoice = Invoice::find($request->id);
        // Los conceptos de esta factura
        $thisInvoiceConcepts = InvoiceConcepts::where('invoice_id', $invoice->id)->get();
        // Título
        $title = "Factura - ". $invoice->reference;

        // Data, para pasar datos
        $data = [
            'title' => $title,
            'invoice_reference' => $invoice['reference'],
        ];
        // Array de conceptos para utilizar en la vista, formatea cadenas para que cuadre
        $invoiceConceptsFormated = array();
        $arrayConceptDescriptionRowsFormated = array();
        foreach($thisInvoiceConcepts as $invoiceConcept){
            // Título
            $invoiceConceptsFormated[$invoiceConcept->id]['title'] = $invoiceConcept['title'];
            // Unidades
            $invoiceConceptsFormated[$invoiceConcept->id]['units'] = $invoiceConcept['units'];

            $invoiceConceptsFormated[$invoiceConcept->id]['price_unit'] = round($invoiceConcept['total']/$invoiceConcept['units'], 2);
            // Precio
            if($invoiceConcept->concept_type_id == BudgetConceptType::TYPE_OWN){

                $invoiceConceptsFormated[$invoiceConcept->id]['subtotal'] = number_format((float)$invoiceConcept->units * $invoiceConcept->sale_price, 2, '.', '');
                $invoiceConceptsFormated[$invoiceConcept->id]['price_unit'] = number_format((float)$invoiceConcept->sale_price, 2, '.', '');
            }
            if($invoiceConcept->concept_type_id == BudgetConceptType::TYPE_SUPPLIER){

                $purchasePriceWithoutMarginBenefit = $invoiceConcept->purchase_price;
                $benefitMargin = $invoiceConcept->benefit_margin;
                $marginBenefitToAdd  =  ($purchasePriceWithoutMarginBenefit*$benefitMargin)/100;
                $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit+ $marginBenefitToAdd;

                if( $purchasePriceWithMarginBenefit != null){
                    $invoiceConceptsFormated[$invoiceConcept->id]['price_unit'] = round((number_format((float)$invoiceConcept->purchase_price, 2, '.', '') / $invoiceConcept->units / 100 * number_format((float)$invoiceConcept->benefit_margin, 2, '.', '')) + (number_format((float)$invoiceConcept->purchase_price, 2, '.', '') / $invoiceConcept->units), 2);
                }
                $invoiceConceptsFormated[$invoiceConcept->id]['subtotal'] = number_format((float)$invoiceConcept->total_no_discount, 2, '.', '');
            }
            // Descuento
            if($invoiceConcept['discount'] == null){
                $invoiceConceptsFormated[$invoiceConcept->id]['discount'] = "0,00";
            }else{
                $invoiceConceptsFormated[$invoiceConcept->id]['discount'] = number_format((float)$invoiceConcept['discount'], 2, ',', '');
            }
            // Total
            $invoiceConceptsFormated[$invoiceConcept->id]['total'] = number_format((float)$invoiceConcept['total'], 2, ',', '');
            // Descripción del concepto tal cual está en base de datos
            $rawConcepts = $invoiceConcept['concept'];
            // Descripción dividida en cadenas y saltos de linea
            $arrayConceptStringsAndBreakLines =  explode(PHP_EOL, $rawConcepts);
            // Recorro el array arrayConceptStringsAndBreakLines y en cada elemento
            // corto la cadena cada 50 caracteres sin partir palabras
            $maxLineLength = 50;
            $charactersInALineCounter = 0;
            $arrayWordsFormated = array();
            $counter = 0;
            $firstWordTempRow = true;
            $counterTempRowsToFormated = 0;
            $stringItemJump = false;
            foreach($arrayConceptStringsAndBreakLines as $stringItem){
                // Una de las cadenas del array que recorremos
                $rowWords = explode(' ', $stringItem);
                $lastWordOfStringArray = end($rowWords);
                $lastWordOfStringArrayKey = key($rowWords);
                // Row temporal
                $tempRow = '';
                // Llenar un array en el que cada elemento será una linea y contara 50 caracteres y no parta una palabra
                foreach($rowWords as $key => $word){
                    // Tamaño de la palabra
                    $wordLength = strlen($word);
                    if($firstWordTempRow == false){
                        if($charactersInALineCounter <=  $maxLineLength ){
                            $tempRow = $tempRow . ' ' . $word;
                            $charactersInALineCounter = $charactersInALineCounter +  $wordLength;
                        }else{
                            // Aquí esta tempRow se mete en el array formated de este concepto
                            // Hasta 50 chars meto en el array la cadena
                            $arrayWordsFormated[$counter] = $tempRow;
                            $counter = $counter + 1;
                            // Lo que sobra lo meto en $tempRow
                            $tempRow =  $word; /*GGGGGG*/
                            $charactersInALineCounter = $wordLength;
                        }
                    }else{
                        $tempRow = $word;
                        $charactersInALineCounter = $charactersInALineCounter +  $wordLength;
                        $firstWordTempRow = false;
                    }
                    if($lastWordOfStringArrayKey == $key ){
                        $arrayWordsFormated[$counter] = $tempRow;
                        $counter = $counter + 1;
                        $charactersInALineCounter = 0;
                        $firstWordTempRow == true;
                    }
                }
            }
            $invoiceConceptsFormated[$invoiceConcept->id]['description'] = $arrayWordsFormated;
        }
        $pdf = PDF::loadView('invoices.previewPDF', compact('invoice', 'data', 'invoiceConceptsFormated'));
        return $pdf->download('factura_' . $invoice['reference'] . '_' . Carbon::now()->format('Y-m-d') . '.pdf');
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
        $new_factura->reference = 'N-' . $invoice->reference;
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
}
