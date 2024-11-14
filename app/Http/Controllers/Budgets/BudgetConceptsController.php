<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use App\Mail\MailConcept;
use App\Mail\MailConceptSupplier;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptSupplierRequest;
use App\Models\Budgets\BudgetConceptSupplierUnits;
use App\Models\Budgets\BudgetCustomPDF;
use App\Models\Clients\Client;
use App\Models\Company\CompanyDetails;
use App\Models\PurcharseOrde\PurcharseOrder;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategories;
use App\Models\Suppliers\Supplier;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BudgetConceptsController extends Controller
{
    /**** Concepto Propio ****/
    public function createTypeOwn(Budget $budget)
    {
        // Obtenemos la informacion a necesaria para mostrar
        $presupuesto = $budget;
        $categorias = ServiceCategories::where('inactive',0)->where('type',2)->get();

        return view('budgets-concepts.creteTypeOwn', compact('categorias', 'presupuesto'));
    }

    public function storeTypeOwn(Request $request, $budget)
    {
        // Validamos los campos
        $this->validate($request, [
            'services_category_id' => 'required|filled',
            'service_id' => 'required',
            'title' => 'required',
            'concept' => 'required',
            'units' => 'required',
            'sale_price' => 'required',
            'total' => 'required',
        ], [
            'services_category_id.required' => 'La categoria del servicio es requerido para continuar',
            'service_id.required' => 'El servicio es requerido para continuar',
            'title.required' => 'El titulo debe ser valido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
            'units.required' => 'Las unidades es requerido para continuar',
            'sale_price.required' => 'El precio de la empresa a es requerido para continuar',
            'total.required' => 'El total es requerido para continuar',
        ]);

        // Construimos la DATA
        $data = $request->all();
        $data['budget_id'] = $budget;
        // Establecemos el tipo (PROVEEDOR) al concepto
        $data['concept_type_id'] = 2;
        // Creamos el concepto
        $conceptoCreate = BudgetConcept::create($data);

        // Variables
        $ivaTotal = 0;
        $total = 0;
        $gross = 0;
        $descuento = 0;
        $base = 0;

        // Obtenemos el presupuesto para Actualizar
        $budgetToUpdate = Budget::where('id', $budget)->first();
        // Obtenemos todos los conceptos de este presupuesto
        $budgetConcepts = BudgetConcept::where('budget_id', $budget)->get();

        if (count($budgetConcepts) >= 1) {
            // Recorremos los conceptos
            foreach ($budgetConcepts as $key => $concept) {
                // Si el concepto es PROVEEDOR
                if ($concept->concept_type_id === 1) {

                }
                // Si el concepto es PROPIO
                elseif($concept->concept_type_id === 2){
                    if ($concept->discount === null) {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $concept->total_no_discount = $grossConcept;
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        $concept->save();


                    }else {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $descuento += $importeConceptDescuento;
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        // Actualizar el concepto
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                }
            }
            // Calculamos el Iva y el Total
            $ivaTotal += ( $base * 21 ) /100;
            $total += $base + $ivaTotal;

            $budgetUpdate = $budgetToUpdate->update([
                'discount' => $descuento,
                'gross' => $gross,
                'base' => $base,
                'iva' => $ivaTotal,
                'total' => $total,

            ]);
        }

        // Mensaje de success
        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El cliente se creo correctamente'
        ]);

        return redirect(route('presupuesto.edit', $budget));
    }

    public function updateTypeOwn(Request $request, $budget)
    {

        $this->validate($request, [
            'services_category_id' => 'required|filled',
            'service_id' => 'required',
            'title' => 'required',
            'concept' => 'required',
            'units' => 'required',
            'sale_price' => 'required',
            'total' => 'required',
        ], [
            'services_category_id.required' => 'La categoria del servicio es requerido para continuar',
            'service_id.required' => 'El servicio es requerido para continuar',
            'title.required' => 'El titulo debe ser valido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
            'units.required' => 'Las unidades es requerido para continuar',
            'sale_price.required' => 'El precio de la empresa a es requerido para continuar',
            'total.required' => 'El total es requerido para continuar',
        ]);

        // Construimos la DATA
        $data = $request->all();
        // Creamos el concepto
        $conceptoCreate = BudgetConcept::where('id', $budget)->first()->update($data);

        // Variables
        $ivaTotal = 0;
        $total = 0;
        $gross = 0;
        $descuento = 0;
        $base = 0;

        $budgetConceptActualizado = BudgetConcept::where('id', $budget)->first();
        // Obtenemos el presupuesto para Actualizar
        $budgetToUpdate = Budget::where('id', $budgetConceptActualizado->budget_id)->first();
        // Obtenemos todos los conceptos de este presupuesto
        $budgetConcepts = BudgetConcept::where('budget_id', $budgetConceptActualizado->budget_id)->get();

        if (count($budgetConcepts) >= 1) {
            // Recorremos los conceptos
            foreach ($budgetConcepts as $key => $concept) {
                // Si el concepto es PROVEEDOR
                if ($concept->concept_type_id === 1) {
                    if ($concept->discount === null) {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->sale_price;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();

                    }else {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept =  $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $descuento += $importeConceptDescuento;
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        // Actualizar el concepto
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                }
                // Si el concepto es PROPIO
                elseif($concept->concept_type_id === 2){
                    if ($concept->discount === null) {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;

                    }else {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $descuento += $importeConceptDescuento;
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        // Actualizar el concepto
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                }
            }
            // Calculamos el Iva y el Total
            $ivaTotal += ( $base * 21 ) /100;
            $total += $base + $ivaTotal;

            $budgetUpdate = $budgetToUpdate->update([
                'discount' => $descuento,
                'gross' => $gross,
                'base' => $base,
                'iva' => $ivaTotal,
                'total' => $total,

            ]);
        }

        // Mensaje de success
        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El concepto se actualizo correctamente'
        ]);

        return redirect(route('presupuesto.edit', $budgetConceptActualizado->budget_id));
    }

    public function editTypeOwn(BudgetConcept $budgetConcept)
    {
        $presupuesto = Budget::where('id', $budgetConcept->budget_id)->get()->first();
        $services = Service::where('inactive',0)->get();
        $serviceCategories = ServiceCategories::where('inactive',0)->where('type',2)->get();


        return view('budgets-concepts.editTypeOwn', compact('budgetConcept', 'presupuesto', 'services', 'serviceCategories'));
    }

    /**** Concepto Proveedor ****/
    public function createTypeSupplier(Budget $budget)
    {
        $presupuesto = $budget;
        $budgetSuppliersSaved = BudgetConceptSupplierRequest::where('budget_concept_id', $presupuesto->id)->get();
        $services = Service::where('inactive',0)->get();
        $serviceCategories = ServiceCategories::where('inactive',0)->where('type',1)->get();

        $suppliers = Supplier::all();
        $budgetSupplierSelectedOption = BudgetConceptSupplierRequest::where('budget_concept_id', $presupuesto->id)->where('selected', 1)->get()->first();

        $categorias = ServiceCategories::where('inactive',0)->where('type',1)->get();
        return view('budgets-concepts.createTypeSupplier', compact(
            'categorias',
            'presupuesto',
            'budgetSuppliersSaved',
            'services',
            'serviceCategories',
            'suppliers',
            'budgetSupplierSelectedOption'
        ));
    }

    public function storeTypeSupplier(Request $request, $budget)
    {
        // dd($request->all());

        // Validamos los campos
        $this->validate($request, [
            'services_category_id' => 'required|exists:services_categories,id',
            'service_id' => 'nullable',
            'title' => 'required',
            'concept' => 'required',
            'units' => 'required|array|min:1',
            'units.*' => 'required|numeric|min:0', // Valida cada elemento del array de unidades
            'supplierId1' => 'required',
            'supplierId2' => 'required',
            'supplierId3' => 'required',
        ], [
            'services_category_id.required' => 'La categoria del servicio es requerido para continuar',
            'service_id.required' => 'El servicio es requerido para continuar',
            'services_category_id.exists' => 'La categoria del servicio es requerido para continuar',
            'service_id.exists' => 'El servicio es requerido para continuar',
            'title.required' => 'El titulo debe ser valido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
            'units.required' => 'Al menos una unidad es requerida para continuar',
            'units.array' => 'Las unidades deben ser proporcionadas.',
            'units.min' => 'Al menos una unidad es requerida.',
            'units.*.required' => 'Este campo de unidades es requerido.',
            'units.*.numeric' => 'Las unidades deben ser numéricas.',
            'units.*.min' => 'Las unidades no pueden ser menores a 0.',
            'supplierId1.required' => 'El proveedor 1 es requerido para continuar',
            'supplierId2.required' => 'El proveedor 2 es requerido para continuar',
            'supplierId3.required' => 'El proveedor 3 es requerido para continuar',
        ]);

        $data = $request->all();
        if($data['service_id'] == 'null' ){
            $data['service_id'] = null;
        }
        $data['budget_id'] = $budget;
        $data['concept_type_id'] = 1;

        // Guarda las unidadesº
        foreach( $data['units'] as $unit ) {
            $budgetConcept = BudgetConcept::create([
                'budget_id' => $budget,
                'concept_type_id' => 1,
                'services_category_id' => $data['services_category_id'],
                'service_id' => $data['service_id'] ?? null,
                'units' => $unit,
                'title' => $data['title'],
                'concept' => $data['concept'],

            ]);

            $budgetConceptSaved = $budgetConcept->save();

            $crearFilaUniadades = BudgetConceptSupplierUnits::create([
                'budget_concept_id' => $budgetConcept->id,
                'units' => $unit,
                'selected' => null
            ]);

            $crearFilaUniadades->save();
            // Guardar las opciones de proveedores
             if( $budgetConceptSaved ) {
                // Proveedor 1
                $newSupplierOpt1 = array(
                    "_token" => $data['_token'],
                    "budget_concept_id" =>$budgetConcept->id,
                    "supplier_id" => $data['supplierId1'],
                    "mail" => $data['supplierEmail1'],
                    "price" => $data['supplierPrice1'],
                    "option_number" => 1,
                );
                $budgetSupplierRequest1 = BudgetConceptSupplierRequest::create($newSupplierOpt1);
                $budgetSupplierRequest1Saved = $budgetSupplierRequest1->save();
                // Proveedor 2
                $newSupplierOpt2 = array(
                    "_token" => $data['_token'],
                    "budget_concept_id" =>$budgetConcept->id,
                    "supplier_id" => $data['supplierId2'],
                    "mail" => $data['supplierEmail2'],
                    "price" => $data['supplierPrice2'],
                    "option_number" => 2,
                );
                $budgetSupplierRequest2 = BudgetConceptSupplierRequest::create($newSupplierOpt2);
                $budgetSupplierRequest2Saved = $budgetSupplierRequest2->save();
                // Proveedor 3

                $newSupplierOpt3 = array(
                    "_token" => $data['_token'],
                    "budget_concept_id" =>$budgetConcept->id,
                    "supplier_id" => $data['supplierId3'],
                    "mail" => $data['supplierEmail3'],
                    "price" => $data['supplierPrice3'],
                    "option_number" => 3,
                );
                $budgetSupplierRequest3 = BudgetConceptSupplierRequest::create($newSupplierOpt3);
                $budgetSupplierRequest3Saved = $budgetSupplierRequest3->save();
            } else {
                return session()->flash('toast', [
                    'icon' => 'error',
                    'mensaje' => "Error en el servidor, intentelo mas tarde."
                ]);
            }
        }


        if(isset($data['checkMail'])) {
            return $this->saveAndSend($budgetConcept,$data['file']);
        } else {

        }
        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El concepto se creo correctamente'
        ]);
        return redirect(route('presupuesto.edit', $budget));
    }

    public function editTypeSupplier(string $id)
    {
        $budgetConcept = BudgetConcept::find($id);
        $arrayEmails = array();
        $suppliers = Supplier::all();
        $presupuesto = Budget::where('id', $budgetConcept->budget_id)->get()->first();
        $budgetSuppliersSaved = BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConcept->id)->get();
        $services = Service::where('services_categories_id', $budgetConcept->services_category_id)->where('inactive',0)->get();
        $categorias = ServiceCategories::where('inactive',0)->where('type',1)->get();
        $client = Client::find($presupuesto->client_id);

        if(!$client->contacto->isEmpty()){
            foreach ($client->contacto as $contact) {
                $arrayEmails[] = $contact->email;
            }
        }

        $budgetSupplierSelectedOption = BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConcept->id)->where('selected', 1)->get()->first();

        return view('budgets-concepts.editTypeSupplier', compact('budgetConcept', 'presupuesto','suppliers', 'budgetSuppliersSaved', 'budgetSupplierSelectedOption', 'services', 'categorias', 'client', 'arrayEmails'));
    }

    public function updateTypeSupplier(Request $request, $budget)
    {

        // Validamos los campos
        $this->validate($request, [
            'services_category_id' => 'required|filled',
            'service_id' => 'nullable',
            'title' => 'required',
            'concept' => 'required',
            'units' => 'required',
            'supplierId1' => 'required',
            'supplierId2' => 'required',
            'supplierId3' => 'required',
        ], [
            'services_category_id.required' => 'La categoria del servicio es requerido para continuar',
            'service_id.required' => 'El servicio es requerido para continuar',
            'title.required' => 'El titulo debe ser valido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
            'units.required' => 'Al menos una unidad es requerida para continuar',
            'supplierId1.required' => 'El proveedor 1 es requerido para continuar',
            'supplierId2.required' => 'El proveedor 2 es requerido para continuar',
            'supplierId3.required' => 'El proveedor 3 es requerido para continuar',
        ]);

        $data = $request->all();
        if($data['service_id'] == 'null' ){
            $data['service_id'] = null;
        }
        $data['total_no_discount'] =  $data['sale_price'];
        $data['total'] =  $data['sale_price'];

        $conceptoactualizado = BudgetConcept::where('id', $budget)->first()->update($data);
        $budgetConceptActualizado = BudgetConcept::where('id', $budget)->first();

        if($conceptoactualizado){
            // Proveedor 1

            if( $data['radioOpt'] == 1){
                $selected = 1;
            }else{
                $selected = 0;
            }
            $newSupplierOpt1 = array(
                "supplier_id" => $data['supplierId1'],
                "mail" => $data['supplierEmail1'],
                "price" => $data['supplierPrice1'],
                "selected" => $selected

            );
            BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConceptActualizado->id)->where('option_number', 1 )->update($newSupplierOpt1);
            // Proveedor 2
            if( $data['radioOpt'] == 2){
                $selected = 1;
            }else{
                $selected = 0;
            }
            $newSupplierOpt2 = array(
                "supplier_id" => $data['supplierId2'],
                "mail" => $data['supplierEmail2'],
                "price" => $data['supplierPrice2'],
                "selected" => $selected

            );
            BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConceptActualizado->id)->where('option_number', 2 )->update($newSupplierOpt2);

            // Proveedor 3
            if( $data['radioOpt'] == 3){
                $selected = 1;
            }else{
                $selected = 0;
            }
            $newSupplierOpt3 = array(
                "supplier_id" => $data['supplierId3'],
                "mail" => $data['supplierEmail3'],
                "price" => $data['supplierPrice3'],
                "selected" => $selected
            );
            BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConceptActualizado->id)->where('option_number', 3 )->update($newSupplierOpt3);

        } else {
            return session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }


        if(isset($data['checkMail'])) {
            return $this->saveAndSend($budgetConceptActualizado,$data['file']);
        } else {

        }

         // Variables
         $ivaTotal = 0;
         $total = 0;
         $gross = 0;
         $descuento = 0;
         $base = 0;

         // Obtenemos el presupuesto para Actualizar
         $budgetToUpdate = Budget::where('id', $budgetConceptActualizado->budget_id)->first();
         // Obtenemos todos los conceptos de este presupuesto
         $budgetConcepts = BudgetConcept::where('budget_id', $budgetConceptActualizado->budget_id)->get();

         if (count($budgetConcepts) >= 1) {
             // Recorremos los conceptos
             foreach ($budgetConcepts as $key => $concept) {
                 // Si el concepto es PROVEEDOR
                 if ($concept->concept_type_id === 1) {
                    if ($concept->discount === null) {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->sale_price;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();

                    }else {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept =  $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $descuento += $importeConceptDescuento;
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        // Actualizar el concepto
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                 }
                 // Si el concepto es PROPIO
                 elseif($concept->concept_type_id === 2){
                     if ($concept->discount === null) {
                         // Calculamos el Bruto del concepto (unidades * precio del concepto)
                         $grossConcept = $concept->units * $concept->sale_price;
                         // Calculamos las Base del Concepto
                         $baseConcept = $grossConcept;
                         // Añadimos la informacion a las variables globales para actualizar presupuesto
                         $gross += $grossConcept;
                         $base += $baseConcept;

                     }else {
                         // Calculamos el Bruto del concepto (unidades * precio del concepto)
                         $grossConcept = $concept->units * $concept->sale_price;
                         // Descuento del concepto
                         $descuentoConcept = $concept->discount;
                         // Calculamos el descuento del concepto
                         $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                         // Calculamos las Base del Concepto
                         $baseConcept = $grossConcept - $importeConceptDescuento;
                         // Añadimos la informacion a las variables globales para actualizar presupuesto
                         $descuento += $importeConceptDescuento;
                         $gross += $grossConcept;
                         $base += $baseConcept;
                         // Actualizar el concepto
                         $concept->total_no_discount = $grossConcept;
                         $concept->total = $baseConcept;
                         $concept->save();
                     }
                 }
             }
             // Calculamos el Iva y el Total
             $ivaTotal += ( $base * 21 ) /100;
             $total += $base + $ivaTotal;

             $budgetUpdate = $budgetToUpdate->update([
                 'discount' => $descuento,
                 'gross' => $gross,
                 'base' => $base,
                 'iva' => $ivaTotal,
                 'total' => $total,

             ]);
         }
        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El concepto se creo correctamente'
        ]);
        return redirect(route('presupuesto.edit', $budgetConceptActualizado->budget_id));
    }
    /**** FUNCIONES GLOBALES ****/

    public function deleteConceptsType(Request $request) {
        $budgetConcept = BudgetConcept::find($request->id);
        if($budgetConcept->concept_type_id == 1){
            if ($budgetConcept->orden) {
                return session()->flash('toast', [
                    'icon' => 'error',
                    'mensaje' => 'No se puede eliminar el concepto porque tiene una orden de compra asociada.'
                ]);
             }
        }
        $budgetID = $budgetConcept->budget_id;

        $budgetConcept->delete();

         // Variables
         $ivaTotal = 0;
         $total = 0;
         $gross = 0;
         $descuento = 0;
         $base = 0;

         // Obtenemos el presupuesto para Actualizar
        $budgetToUpdate = Budget::where('id', $budgetID)->first();
         // Obtenemos todos los conceptos de este presupuesto
        $budgetConcepts = BudgetConcept::where('budget_id', $budgetID)->get();

        if (count($budgetConcepts) >= 1) {
             // Recorremos los conceptos
            foreach ($budgetConcepts as $key => $concept) {
                 // Si el concepto es PROVEEDOR
                if ($concept->concept_type_id === 1) {

                }
                 // Si el concepto es PROPIO
                elseif($concept->concept_type_id === 2){
                    if ($concept->discount === null) {
                         // Calculamos el Bruto del concepto (unidades * precio del concepto)
                         $grossConcept = $concept->units * $concept->sale_price;
                         // Calculamos las Base del Concepto
                         $baseConcept = $grossConcept;
                         // Añadimos la informacion a las variables globales para actualizar presupuesto
                         $gross += $grossConcept;
                         $base += $baseConcept;

                    }else {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        $descuento += $importeConceptDescuento;
                        // Actualizar el concepto
                        $concept->discount = $concept->discount;
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                }
            }
            // Calculamos el Iva y el Total
            $ivaTotal += ( $base * 21 ) /100;
            $total += $base + $ivaTotal;

            $budgetUpdate = $budgetToUpdate->update([
                'discount' => $descuento,
                'gross' => $gross,
                'base' => $base,
                'iva' => $ivaTotal,
                'total' => $total,

            ]);

        }
        return session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El concepto se elimino correctamente'
        ]);
    }

    public function discountUpdate(Request $request){
        $budgetConceptID = $request->idConcept;
        $budgetID = $request->idBudget;
        $discount = $request->discount;

        $conceptUpdate = BudgetConcept::find($budgetConceptID);
        if ($conceptUpdate) {
            $conceptUpdate->discount = $discount;
        }
        $conceptUpdate->save();


        // Variables
        $ivaTotal = 0;
        $total = 0;
        $gross = 0;
        $descuento = 0;
        $base = 0;

        // Obtenemos el presupuesto para Actualizar
        $budgetToUpdate = Budget::where('id', $budgetID)->first();
        // Obtenemos todos los conceptos de este presupuesto
        $budgetConcepts = BudgetConcept::where('budget_id', $budgetID)->get();

        if (count($budgetConcepts) >= 1) {
            // Recorremos los conceptos
            foreach ($budgetConcepts as $key => $concept) {
                // Si el concepto es PROVEEDOR
                if ($concept->concept_type_id === 1) {
                    if ($concept->discount === null) {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->sale_price;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();

                    }else {
                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept =  $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $descuento += $importeConceptDescuento;
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        // Actualizar el concepto
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                }
                // Si el concepto es PROPIO
                elseif($concept->concept_type_id === 2){
                    if ($concept->discount === null) {

                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;

                    }else {

                        // Calculamos el Bruto del concepto (unidades * precio del concepto)
                        $grossConcept = $concept->units * $concept->sale_price;
                        // Descuento del concepto
                        $descuentoConcept = $concept->discount;
                        // Calculamos el descuento del concepto
                        $importeConceptDescuento = ( $grossConcept * $descuentoConcept ) / 100;
                        // Calculamos las Base del Concepto
                        $baseConcept = $grossConcept - $importeConceptDescuento;
                        // Añadimos la informacion a las variables globales para actualizar presupuesto
                        $gross += $grossConcept;
                        $base += $baseConcept;
                        $descuento += $importeConceptDescuento;
                        // Actualizar el concepto
                        $concept->total_no_discount = $grossConcept;
                        $concept->total = $baseConcept;
                        $concept->save();
                    }
                }
            }
            // Calculamos el Iva y el Total
            $ivaTotal += ( $base * 21 ) /100;
            $total += $base + $ivaTotal;

            $budgetUpdate = $budgetToUpdate->update([
                'discount' => $descuento,
                'gross' => $gross,
                'base' => $base,
                'iva' => $ivaTotal,
                'total' => $total,

            ]);

        }
        return session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El concepto se actualizo correctamente'
        ]);
    }


    /**** Metodos GET ****/
    public function getServicesByCategory($categoryId)
    {
        $services = Service::where('services_categories_id', $categoryId)->where('inactive',0)
            ->get(['id', 'title', 'concept', 'price'])
            ->toArray();

        return response()->json($services);
    }

    public function getInfoByServices(Request $request)
    {
        $categoryId = $request->input('categoryId');
        $service = Service::find( $categoryId);
        $empresa = CompanyDetails::find(1);
        $preciomedi  = $service->calcularPrecioMedio($empresa->price_hour);
        $services = Service::where('id', $categoryId)
            ->get(['id', 'title', 'concept', 'price'])
            ->toArray();
        if($preciomedi > 0){
             $services['0']['preciomedi'] = $preciomedi;
        }else{
             $services['0']['preciomedi'] = null;
        }

        return response()->json($services);
    }

    public function saveAndSend(BudgetConcept $budgetConcept,$file){
        //Enviar los emails
        $budgetSuppliersSaved = BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConcept->id)->get();

        $mailConcept = new \stdClass();
        $mailConcept->gestor = Auth::user()->name." ".Auth::user()->surname;
        $mailConcept->gestorMail = Auth::user()->email;
        $mailConcept->gestorTel = '956 662 942';
        $mailConcept->cantidad = $budgetConcept->units;
        $mailConcept->titulo = $budgetConcept->title;
        $mailConcept->concepto = $budgetConcept->concept;
        $mailConcept->files = false;

        foreach($budgetSuppliersSaved as $budgetSupplier)
        {
            $mailsBCC[] = $budgetSupplier->mail;
        }



        $data = [];
        if($file[0] !== null)
        {
            foreach($file as $fileNew)
            {
                $path = Storage::putFileAs('app',$fileNew, 'supplier-'.time().'.'.$fileNew->getClientOriginalExtension());
                $data[] = "/home/crmhawki/public_html/storage/app/".$path;
            }
            $mailConcept->files = true;
        }

        $email = new MailConcept($mailConcept,$data);


        Mail::to($mailConcept->gestorMail)
        ->bcc($mailsBCC)
        ->cc([])
        ->send($email);

    }

    public function generatePurchaseOrder( $id){

        $budgetConcept = BudgetConcept::find($id);
        if ($budgetConcept->id){

            $searchOrder = PurcharseOrder::where("budget_concept_id", $budgetConcept->id)->first();
            if($searchOrder){
                $budgetConceptSupplier = BudgetConceptSupplierRequest::where("budget_concept_id", $budgetConcept->id)->where("selected", 1)->first();

                $searchOrder->supplier_id = $budgetConceptSupplier->supplier_id;
                $searchOrder->client_id = $budgetConcept->presupuesto->client_id;
                $searchOrder->project_id = $budgetConcept->presupuesto->project_id;
                $searchOrder->payment_method_id = $budgetConcept->presupuesto->payment_method_id;
                $searchOrder->units = $budgetConcept->units;
                $searchOrder->amount = $budgetConcept->total;
                $searchOrder->note = $budgetConcept->concept;
                $searchOrder->sent = 0;
                $searchOrder->cancelled = 0;

                $searchOrder->save();


                return response()->json([
                    'message' => 'Orden de compra actualizada',
                    'entryUrl' => route('purchase_order.purchaseOrderPDF',  $searchOrder),
                    ]);

            }else{
                $hoy = Carbon::now();
                $hoy->format('d/m/Y');
                $parseHoy = Carbon::parse($hoy);
                $budgetConceptSupplier = BudgetConceptSupplierRequest::where("budget_concept_id", $budgetConcept->id)->where("selected", 1)->first();

                $savedPurchaseOrder = new PurcharseOrder;
                $savedPurchaseOrder->supplier_id = $budgetConceptSupplier->supplier_id;
                $savedPurchaseOrder->budget_concept_id = $budgetConcept->id;
                $savedPurchaseOrder->client_id = $budgetConcept->presupuesto->client_id;
                $savedPurchaseOrder->project_id = $budgetConcept->presupuesto->project_id;
                $savedPurchaseOrder->payment_method_id = $budgetConcept->presupuesto->payment_method_id;
                // $savedPurchaseOrder->bank_id = ???
                $savedPurchaseOrder->units = $budgetConcept->units;
                $savedPurchaseOrder->amount = $budgetConcept->total;
                $savedPurchaseOrder->shipping_date = $parseHoy;
                $savedPurchaseOrder->note = $budgetConcept->concept;
                $savedPurchaseOrder->sent = 0;
                $savedPurchaseOrder->cancelled = 0;

                $savedPurchaseOrder->save();

                return response()->json([
                    'message' => 'Orden de compra generada',
                    'entryUrl' => route('purchase_order.purchaseOrderPDF',  $savedPurchaseOrder),
                    ]);
            }
        }else{
            return response()->json("Error en la generación de la orden de compra", "Error");
        }
    }


    public function saveOrderForSend(Request $request){

        $budgetConcept = BudgetConcept::where("id",$request->id)->first();

        if ($budgetConcept->id){

            $searchOrder = PurcharseOrder::where("budget_concept_id", $budgetConcept->id)->first();
            if($searchOrder){
                $budgetConceptSupplier = BudgetConceptSupplierRequest::where("budget_concept_id", $budgetConcept->id)->where("selected", 1)->first();

                $searchOrder->supplier_id = $budgetConceptSupplier->supplier_id;
                $searchOrder->client_id = $budgetConcept->presupuesto->client_id;
                $searchOrder->project_id = $budgetConcept->presupuesto->project_id;
                $searchOrder->payment_method_id = $budgetConcept->presupuesto->payment_method_id;
                $searchOrder->units = $budgetConcept->units;
                $searchOrder->amount = $budgetConcept->total;
                $searchOrder->note = $budgetConcept->concept;
                $searchOrder->sent = 0;
                $searchOrder->cancelled = 0;

                $searchOrder->save();

                return $this->generatePDFAndSend($searchOrder, $request);

            }else{

                $hoy = Carbon::now();
                $hoy->format('d/m/Y');
                $parseHoy = Carbon::parse($hoy);
                $budgetConceptSupplier = BudgetConceptSupplierRequest::where("budget_concept_id", $budgetConcept->id)->where("selected", 1)->first();

                $savedPurchaseOrder = new PurcharseOrder;
                $savedPurchaseOrder->supplier_id = $budgetConceptSupplier->supplier_id;
                $savedPurchaseOrder->budget_concept_id = $budgetConcept->id;
                $savedPurchaseOrder->client_id = $budgetConcept->presupuesto->client_id;
                $savedPurchaseOrder->project_id = $budgetConcept->presupuesto->project_id;
                $savedPurchaseOrder->payment_method_id = $budgetConcept->presupuesto->payment_method_id;
                // $savedPurchaseOrder->bank_id = ???
                $savedPurchaseOrder->units = $budgetConcept->units;
                $savedPurchaseOrder->amount = $budgetConcept->total;
                $savedPurchaseOrder->shipping_date = $parseHoy;
                $savedPurchaseOrder->note = $budgetConcept->concept;
                $savedPurchaseOrder->sent = 0;
                $savedPurchaseOrder->cancelled = 0;

                $savedPurchaseOrder->save();

                return $this->generatePDFAndSend($savedPurchaseOrder, $request);


            }
        }else{
            return response()->json("Error en la generación de la orden de compra", "Error");
        }
    }


    public function generatePDFAndSend(PurcharseOrder $order, Request $request){

        set_time_limit(300);

        $pathFiles = array();
        $mailConcept = new \stdClass();

        $request->validate(rules: [
            'files.*' => 'max:10000'
        ]);

        $preForm = $request->all();

        foreach($preForm as $key => $item){
            if(!$item){
                $preForm[$key] = "";
            }
        }

        // PDF personalización
        $budgetCustomPDF = BudgetCustomPDF::where('id', 1)->get()->first();

        $supplier = Supplier::where('id',$order->supplier_id)->get()->first();

        $name = $budgetCustomPDF->company_name;

        $proveedor = $order->Proveedor->name;

        $referencia = DB::table('budgets')
        ->join('budget_concepts', 'budget_concepts.budget_id', '=', 'budgets.id')
        ->join('purchase_order', 'purchase_order.budget_concept_id', '=', 'budget_concepts.id')
        ->where('purchase_order.id', '=', $order->id)
        ->value('reference');

        $logoURL =  config('app.appUrl') . $budgetCustomPDF['logo_image'];

        $arrayConceptStringsAndBreakLines = explode(PHP_EOL, $order->concepto->concept);

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

        $formatedConcept = $arrayWordsFormated;
        $amount = (float)$order->concepto->purchase_price;
        $precio = number_format($amount, 2, ',', '');

        $data = [
            'name' => $name,
            'supplier' => $proveedor,
            'date' => $order->shipping_date,
            'reference' => $referencia,
            'pay_method' => $order->payMethod->name ?? '',
            "ref_order" => $order->id,
            "concept" => $formatedConcept,
            "units" => $order->units,
            "import_order" => $precio,
            "concept_budget" => $order->concepto->concept ?? '',
            "title_budget" => $order->concepto->title ?? '',
            "client_name" => $preForm['client_empresa'] ?? '',
            "company" => $preForm['company'] ?? '',
            "phone" => $preForm['telefono'] ?? '',
            "address" => $preForm['address'] ?? '',
            "city" => $preForm['ciudad'] ?? '',
            "province" => $preForm['provincia'] ?? '',
            "cp" => $preForm['cp'] ?? '',
        ];


        $name = 'orden_compra_' .$order->id . '_' . Carbon::now()->format('Y-m-d');

        $encrypted = $this->encrypt_decrypt('encrypt', $name);


        // Generate the PDF file for the supplier
        $pathToSaveSupplier = 'Ordenes/' . $encrypted . '.pdf';
        $path = Storage::disk('public')->put($pathToSaveSupplier, PDF::loadView('purchase_order.purchaseOrderPDF', compact('data', 'logoURL', 'budgetCustomPDF'))->output());
        $fileUrl = Storage::url($pathToSaveSupplier);
        // Add the supplier file path to the array
        $pathFiles[] =  storage_path('app/public/' . $pathToSaveSupplier);

        // Generate the name and path for the delivery order (albarán)
        $nameAlbaran = 'albaran_' . $order->id . '_' . Carbon::now()->format('Y-m-d');
        $pathToSaveAlbaran = 'Albaranes/' . $nameAlbaran . '.pdf';
        Storage::disk('public')->put($pathToSaveAlbaran, PDF::loadView('purchase_order.deliveryOrderPDF', compact('data', 'logoURL', 'budgetCustomPDF'))->output());
        $fileUrl2 = storage_path('app/public/' . $pathToSaveAlbaran);

        // Add the delivery order path to the array
        $pathFiles[] = $fileUrl2;
        if($request->url){
            $mailConcept->url = $request->url;
        }else{
            $mailConcept->url = null;
        }

        $mailConcept->gestor = Auth::user()->name." ".Auth::user()->surname;
        $mailConcept->gestorMail = Auth::user()->email;
        $mailConcept->gestorTel = '956 662 942';

        // if($request->hasFile('files')){
        //     foreach($request->file('files') as $fileNew){
        //         Storage::disk('temp')->put($fileNew->getClientOriginalName(), \File::get($fileNew));
        //         $path = Storage::disk('temp')->path($fileNew->getClientOriginalName());
        //         $pathFiles[] = $path;
        //     }
        // }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $fileNew) {
                // Generar un nombre único para el archivo
                $fileName = time() . '_' . $fileNew->getClientOriginalName();
                // Guardar el archivo en el disco temporal
                $path = Storage::disk('temp')->put($fileName, file_get_contents($fileNew));
                // Obtener la ruta absoluta del archivo guardado
                $absolutePath = storage_path('app/temp/' . $fileName);
                $pathFiles[] = $absolutePath;
            }
        }

        $mailsBCC[] = "emma@lchawkins.com";
        $mailsBCC[] = "ivan@lchawkins.com";
        $mailsBCC[] = Auth::user()->email;
        $supplierMail = BudgetConceptSupplierRequest::where('budget_concept_id',$order->budget_concept_id)->where('selected',1)->first()->mail;
        $email = new MailConceptSupplier($mailConcept, $pathFiles);

        try {
            // Enviar el correo
            Mail::to($supplierMail)->bcc($mailsBCC)->send($email);

            // Eliminar los archivos temporales después de enviar el correo
            foreach ($pathFiles as $file) {
                Storage::delete($file);
            }

            return response()->json(['success' => true, 'message' => 'Correo enviado exitosamente.']);
        } catch (\Exception $e) {
            // Manejo de error al enviar el correo
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function generatePDF($id){
        // PDF personalización
        $order = PurcharseOrder::find($id);
        $budgetCustomPDF = BudgetCustomPDF::where('id', 1)->get()->first();

        $name = $budgetCustomPDF->company_name;

        $proveedor = $order->Proveedor->name;

        $referencia = Budget::join('budget_concepts', 'budget_concepts.budget_id', '=', 'budgets.id')
            ->join('purchase_order', 'purchase_order.budget_concept_id', '=', 'budget_concepts.id')
            ->where('purchase_order.id', '=', $order->id)
            ->value('reference');

        $logoURL =  config('app.appUrl') . $budgetCustomPDF['logo_image'];

        $arrayConceptStringsAndBreakLines = explode(PHP_EOL, $order->concepto->concept);

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

        $formatedConcept = $arrayWordsFormated;


        $amount = (float)$order->concepto->purchase_price;

        $precio = number_format($amount, 2, ',', '');

        $data = [
            'name' => $name,
            'supplier' => $proveedor,
            'date' => $order->shipping_date,
            'reference' => $referencia,
            'pay_method' => $order->payMethod->name,
            "ref_order" => $order->id,
            "concept" => $formatedConcept,
            "units" => $order->units,
            "import_order" => $precio,
            "concept_budget" => $order->concepto->concept,
            "title_budget" => $order->concepto->title,
        ];


        $pdf = PDF::loadView('purchase_order.purchaseOrderPDF', compact('data','logoURL', 'budgetCustomPDF'));

        return $pdf->download('orden_compra_' .$order->id . '_' . Carbon::now()->format('Y-m-d') . '.pdf');


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

}
