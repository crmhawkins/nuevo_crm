<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptSupplierRequest;
use App\Models\Budgets\BudgetConceptSupplierUnits;
use App\Models\Clients\Client;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategories;
use App\Models\Suppliers\Supplier;
use Illuminate\Http\Request;

class BudgetConceptsController extends Controller
{
    /**** Concepto Propio ****/
    public function createTypeOwn(Budget $budget)
    {
        // Obtenemos la informacion a necesaria para mostrar
        $presupuesto = $budget;
        $categorias = ServiceCategories::all();

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
            'mensaje' => 'El cliente se creo correctamente'
        ]);

        return redirect(route('presupuesto.edit', $budget));
    }

    public function updateTypeOwn(Request $request, $budget)
    {
        // dd($budget);
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
        $services = Service::All();
        $serviceCategories = ServiceCategories::All();

        return view('budgets-concepts.editTypeOwn', compact('budgetConcept', 'presupuesto', 'services', 'serviceCategories'));
    }

    /**** Concepto Proveedor ****/
    public function createTypeSupplier(Budget $budget)
    {
        $presupuesto = $budget;
        $budgetSuppliersSaved = BudgetConceptSupplierRequest::where('budget_concept_id', $presupuesto->id)->get();
        $services = Service::all();
        $serviceCategories = ServiceCategories::all();
        $suppliers = Supplier::all();
        $budgetSupplierSelectedOption = BudgetConceptSupplierRequest::where('budget_concept_id', $presupuesto->id)->where('selected', 1)->get()->first();

        $categorias = ServiceCategories::all();
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
            'services_category_id' => 'required|filled',
            'service_id' => 'required',
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
        $data['budget_id'] = $budget;
        $data['concept_type_id'] = 1;

        // Guardar
        $budgetConcept = BudgetConcept::create([
            'budget_id' => $budget,
            'concept_type_id' => 1,
            'services_category_id' => $data['services_category_id'],
            'service_id' => $data['service_id'],
            'title' => $data['title'],
            'concept' => $data['concept'],

        ]);

        $budgetConceptSaved = $budgetConcept->save();

        // Guarda las unidades
        foreach( $data['units'] as $unit ) {
            $crearFilaUniadades = BudgetConceptSupplierUnits::create([
                'budget_concept_id' => $budgetConcept->id,
                'units' => $unit,
                'selected' => null
            ]);
            $crearFilaUniadades->save();
        }

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

    public function editTypeSupplier(BudgetConcept $budgetConcept)
    {
        $arrayEmails = array();
        $suppliers = Supplier::all();
        $budget = Budget::where('id', $budgetConcept->budget_id)->get()->first();
        $budgetSuppliersSaved = BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConcept->id)->get();
        $services = Service::All();
        $serviceCategories = ServiceCategories::All();
        $client = Client::find($budget->client_id);

        if(!$client->contacts->isEmpty()){
            foreach ($client->contacts as $contact) {
                $arrayEmails[] = $contact->email;
            }
        }

        $budgetSupplierSelectedOption = BudgetConceptSupplierRequest::where('budget_concept_id', $budgetConcept->id)->where('selected', 1)->get()->first();

        return view('budget-concepts.editTypeSupplier', compact('budgetConcept', 'budget','suppliers', 'budgetSuppliersSaved', 'budgetSupplierSelectedOption', 'services', 'serviceCategories', 'client', 'arrayEmails'));
    }

    /**** FUNCIONES GLOBALES ****/

    public function deleteConceptsType(Request $request) {
        $budgetConcept = BudgetConcept::find($request->id);
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
        $services = Service::where('services_categories_id', $categoryId)
            ->get(['id', 'title', 'concept', 'price'])
            ->toArray();

        return response()->json($services);
    }

    public function getInfoByServices(Request $request)
    {
        $categoryId = $request->input('categoryId');

        $services = Service::where('id', $categoryId)
            ->get(['id', 'title', 'concept', 'price'])
            ->toArray();

        return response()->json($services);
    }
}
