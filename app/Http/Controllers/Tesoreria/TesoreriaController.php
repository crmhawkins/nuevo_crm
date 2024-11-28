<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\CategoriaGastosAsociados;
use App\Models\Accounting\CategoriaGastos;
use App\Models\Accounting\Gasto;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\Ingreso;
use App\Models\Accounting\Iva;
use App\Models\Accounting\Traspaso;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use App\Models\Other\BankAccounts;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\PurcharseOrde\PurcharseOrder;
use Carbon\Carbon;

class TesoreriaController extends Controller
{

    //index
    public function indexIngresos(){
        return view('tesoreria.ingresos.index');
    }

    public function indexGastos(){
        return view('tesoreria.gastos.index');
    }

    public function indexUnclassifiedExpensese(Request $request){
        return view('tesoreria.gastos-sin-clasificar.index');
    }

    public function indexAssociatedExpenses(){
        return view('tesoreria.gastos-asociados.index');
    }

    public function indexTraspasos(){
        return view('tesoreria.traspasos.index');
    }

    //create
    public function createIngresos(){
        $banks = BankAccounts::all();
        $invoices = Invoice::all();
        return view('tesoreria.ingresos.create', compact('banks','invoices'));
    }

    public function createTraspasos(){
        $banks = BankAccounts::all();
        return view('tesoreria.traspasos.create', compact('banks'));
    }

    public function createGastos(){
        $tiposIva = Iva::all();
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $categorias = CategoriaGastos::all();

        return view('tesoreria.gastos.create',compact( 'banks', 'paymentMethods','tiposIva','categorias'));
    }

    public function createAssociatedExpenses(){
        $tiposIva = Iva::all();
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $purchaseOrders = PurcharseOrder::doesntHave('associatedExpense')->get();
        $categorias = CategoriaGastosAsociados::all();

        return view('tesoreria.gastos-asociados.create',compact( 'banks', 'paymentMethods','purchaseOrders','tiposIva','categorias'));
    }

    //edit
    public function editTraspasos(string $id){
        $traspaso = Traspaso::find($id);
        if (!$traspaso) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El Traspaso no existe'
            ]);
            return redirect()->route('traspaso.index');
        }
        $banks = BankAccounts::all();
        return view('tesoreria.traspasos.edit', compact('traspaso','banks'));
    }

    public function editIngresos(string $id){
        $ingreso = Ingreso::find($id);
        if (!$ingreso) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El Ingreso no existe'
            ]);
            return redirect()->route('ingreso.index');
        }
        $banks = BankAccounts::all();
        $invoices = Invoice::all();

        return view('tesoreria.ingresos.edit',compact('ingreso','banks','invoices'));
    }

    public function editGastos(string $id){
        $tiposIva = Iva::all();
        $gasto = Gasto::find($id);
        if (!$gasto) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El gasto no existe'
            ]);
            return redirect()->route('gasto.index');
        }
        // Obtener listas de opciones necesarias para el formulario
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $categorias = CategoriaGastos::all();

        return view('tesoreria.gastos.edit', compact('gasto', 'banks', 'paymentMethods','tiposIva','categorias'));

    }

    public function editUnclassifiedExpensese(string $id){

        $unclassifiedExpense = UnclassifiedExpenses::find($id);
        if (!$unclassifiedExpense) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El gasto no existe'
            ]);
            return redirect()->route('gasto-sin-clasificar.index');
        }

        // Obtener listas de opciones necesarias para el formulario
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $budgets = Budget::whereIn('budget_status_id', [3,5,6,7])->get();
        $purchaseOrders = PurcharseOrder::doesntHave('associatedExpense')->get();

        return view('tesoreria.gastos-sin-clasificar.edit', compact('unclassifiedExpense', 'banks', 'paymentMethods', 'budgets', 'purchaseOrders'));
    }

    public function editAssociatedExpenses(string $id){
        $tiposIva = Iva::all();

        $gasto = AssociatedExpenses::find($id);
        if (!$gasto) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El gasto no existe'
            ]);
            return redirect()->route('gasto-asociados.index');
        }

        // Obtener listas de opciones necesarias para el formulario
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $budgets = Budget::all();
        $categorias = CategoriaGastosAsociados::all();

        $purchaseOrders = PurcharseOrder::where(function($query) use ($gasto) {
            $query->doesntHave('associatedExpense')
                  ->orWhere('id', $gasto->purchase_order_id);
        })->get();
        return view('tesoreria.gastos-asociados.edit',compact('gasto', 'banks', 'paymentMethods', 'budgets', 'purchaseOrders','tiposIva','categorias'));
    }


    //Store
    public function storeTraspasos(Request $request ){

        $validated = $this->validate($request, [
            'from_bank_id' => 'required',
            'to_bank_id' => 'required',
            'amount' => 'required',
            'fecha' => 'required',
        ],[
            'from_bank_id.required' => 'El banco de origen es obligatorio.',
            'to_bank_id.required' => 'El banco de destino es obligatorio.',
            'amount.required' => 'El monto es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
        ]);
        $traspaso = new Traspaso( $validated);
        $traspaso->save();

        return redirect()->route('traspasos.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Se ha creado el traspaso con exito'
        ]);
    }

    public function storeIngresos(Request $request ){
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',
            'date' => 'required',
        ],[
            'title.required' => 'El título es obligatorio.',
            'date.required' => 'La fecha es obligatoria.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'invoice_id.integer' => 'El ID de la factura debe ser un número entero.',
            'invoice_id.exists' => 'El ID de la factura proporcionado no existe.',
        ]);

        $ingreso = new Ingreso( $validated);
        $ingreso->save();
        return redirect()->route('ingreso.edit', $ingreso->id)->with('toast',[
            'icon' => 'success',
            'mensaje' => 'Ingreso creado exitosamente'
        ]);

    }

    public function storeGastos(Request $request){

        $unclassifiedExpenses = UnclassifiedExpenses::find($request->id);
        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'reference' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'date' => 'nullable',
            'received_date' => 'nullable',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'transfer_movement' => 'nullable',
            'state' => 'required|string|max:255',
            'documents' => 'nullable',
            'iva' => 'nullable',
            'categoria_id' => 'nullable',
        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'state.max' => 'El estado no debe exceder los 255 caracteres.',
        ]);
        $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');

        // Crear el gasto asociado
        $gasto = new Gasto( $validated);

        // Asignar valores adicionales si es necesario
        if ($request->hasFile('documents') && $request->file('documents')->isValid()) {
            $path = $request->file('documents')->store('documents', 'public');
            $gasto->documents = $path;
        }
        // Asignar el valor de transfer_movement de forma correcta
        $gasto->transfer_movement = $request->has('transfer_movement') ? 1 : 0;
        // Guardar el nuevo gasto
        $gasto->save();

        if(isset($unclassifiedExpenses)){
            $unclassifiedExpenses->accepted = true;
            $unclassifiedExpenses->save();
        }
        // Redireccionar con mensaje de éxito
        return redirect()->route('gasto.edit', $gasto->id)->with('success', 'Gasto asociado creado exitosamente.');
    }

    public function storeAssociatedExpenses(Request $request)
    {

        $unclassifiedExpenses = UnclassifiedExpenses::find($request->id);

        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'received_date' => 'nullable',
            'reference' => 'required|string|max:255',
            'date' => 'nullable',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'purchase_order_id' => 'required|integer|exists:purchase_order,id',
            'state' => 'required|string|max:255',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'aceptado_gestor' => 'nullable|boolean',
            'documents' => 'nullable',
            'iva' => 'nullable',
            'categoria_id' => 'nullable',
            'total' => 'nullable',
        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'purchase_order_id.required' => 'El ID de la orden de compra es obligatorio.',
            'purchase_order_id.integer' => 'El ID de la orden de compra debe ser un número entero.',
            'purchase_order_id.exists' => 'El ID de la orden de compra proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'state.max' => 'El estado no debe exceder los 255 caracteres.',
            'aceptado_gestor.boolean' => 'El campo aceptado gestor debe ser verdadero o falso.',
        ]);

        $purchaseOrder = PurcharseOrder::find($validated['purchase_order_id']);
        $precio = $purchaseOrder->concepto->purchase_price;

        if($validated['quantity'] != $precio){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'La cantidad no coincide con la cantidad de la orden de compra'
            ]);
        }
        $validated['total'] = number_format(($validated['quantity']  * $validated['iva'] / 100) + $validated['quantity'], 2, '.', '');
        $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');        // Crear el gasto asociado
        $associatedExpense = new AssociatedExpenses( $validated);

        // Asignar valores adicionales si es necesario
        // Ejemplo: Manejar la carga de archivos
        if ($request->hasFile('documents')) {
            $path = $request->file('documents')->store('documents', 'public');
            $associatedExpense->documents = $path;
        }
        // Guardar el nuevo gasto
        $associatedExpense->save();

        if(isset($unclassifiedExpenses)){
            $unclassifiedExpenses->accepted = true;
            $unclassifiedExpenses->save();
        }
        // Redireccionar con mensaje de éxito
        return redirect()->route('gasto-asociado.edit', $associatedExpense->id)->with('success', 'Gasto asociado creado exitosamente.');
    }

    public function storeUnclassifiedExpensese(Request $request){
        // Crear un gasto sin clasificar
       if ($request->hasFile('file')) {
           $path = $request->file('file')->store('documents', 'public');
                   $unclassifiedExpense = UnclassifiedExpenses::create(array_merge(
           $request->all(),
           ['documents' => $path]
           ));
       }else{
            $unclassifiedExpense = UnclassifiedExpenses::create($request->all());
       }

       if(isset($unclassifiedExpense->order_number)){

           $purchaseOrder = PurcharseOrder::where('id', $request->order_number)
           ->where('amount', $request->amount)
           ->first();

           if ($purchaseOrder){
               $associatedExpense = AssociatedExpenses::create([
                   'title' => $unclassifiedExpense->pdf_file_name,
                   'reference' => $unclassifiedExpense->invoice_number,
                   'quantity' => $unclassifiedExpense->amount,
                   'bank_id' => $purchaseOrder->bank_id,
                   'received_date' => $unclassifiedExpense->received_date,
                   'payment_method_id' => $purchaseOrder->payment_method_id,
                   'budget_id' => $purchaseOrder->concepto->presupuesto->id,
                   'purchase_order_id' => $purchaseOrder->id,
                   'state' => 'PENDIENTE', // Estado inicial, puedes cambiarlo si es necesario
                   'aceptado_gestor' => false,
                   'documents' => $unclassifiedExpense->documents,

               ]);
               if($associatedExpense){
                   $unclassifiedExpense->message = 'Generado Gasto Asociado';
                   $unclassifiedExpense->save();
                   return response()->json([
                       'success' => true,
                       'message' => 'Gasto registrado y gasto asociado creado correctamente.'
                   ], 201);
               }
           }else{
               $purchaseOrder = PurcharseOrder::where('id', $request->order_number)
               ->first();
               if($purchaseOrder){
                   $unclassifiedExpense->message = 'El valor de la factura no coincide con la orden de compra';
                   $unclassifiedExpense->save();
               }else{
                   $unclassifiedExpense->message = 'No se encontro orden de compra con este numero de orden';
                   $unclassifiedExpense->save();
               }
               return response()->json([
                   'success' => true,
                   'message' => 'Gasto registrado correctamente.'
               ], 201);
           }
       }else{
           $unclassifiedExpense->message = 'No se encontro numero de orden en la factura';
           $unclassifiedExpense->save();
           return response()->json([
               'success' => true,
               'message' => 'Gasto registrado correctamente.'
           ], 201);
       }
    }
    //update
    public function updateTraspasos(Request $request, string $id){
        $traspaso = Traspaso::find($id);
        if (!$traspaso) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El Traspaso no existe'
            ]);
            return redirect()->route('traspaso.index');
        }
        $validated = $this->validate($request, [
            'from_bank_id' => 'required',
            'to_bank_id' => 'required',
            'amount' => 'required',
            'fecha' => 'required',
        ],[
            'from_bank_id.required' => 'El banco de origen es obligatorio.',
            'to_bank_id.required' => 'El banco de destino es obligatorio.',
            'amount.required' => 'El monto es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
        ]);
        $traspaso->update($validated);
        return redirect()->route('traspasos.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Se ha actualizado el traspaso con exito'
        ]);;
    }

    public function updateIngresos(Request $request, string $id){
        $ingreso = Ingreso::find($id);

        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'date' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',
        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'date.required' => 'La fecha es obligatoria.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'invoice_id.integer' => 'El ID de la factura debe ser un número entero.',
            'invoice_id.exists' => 'El ID de la factura proporcionado no existe.',
        ]);
        $ingresoUpdated = $ingreso->update($validated);

        if($ingresoUpdated){
            return redirect()->route('ingreso.index', $ingreso->id)->with('toast',[
                'icon' => 'success',
                'mensaje' => 'Ingreso actualizado exitosamente'
            ]);
        }else{
            return redirect()->back()->with('toast',[
                'icon' => 'error',
                'mensaje' => 'Error al actualizar el ingreso'
            ]);

        }

    }

    public function updateGastos(Request $request, string $id){
        $gasto = Gasto::find($id);

        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'reference' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'transfer_movement' => 'nullable',
            'state' => 'required|string|max:255',
            'iva' => 'nullable',
            'categoria_id' => 'nullable',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'state.max' => 'El estado no debe exceder los 255 caracteres.',
        ]);
        $validated['transfer_movement'] = $request->has('transfer_movement') ? 1 : 0;
        $validated['date'] = Carbon::parse($validated['date'])->format('Y-m-d');

        // Actualizar el gasto con los datos validados
        $gasto->update($validated);

        // Manejar la carga de archivos
        if ($request->hasFile('documents') && $request->file('documents')->isValid()) {
            $path = $request->file('documents')->store('documents', 'public');
            $gasto->documents = $path;
        }

        // Guardar los cambios
        $gasto->save();

        // Redireccionar con mensaje de éxito
        return redirect()->route('gasto.index')->with('success', 'Gasto actualizado exitosamente.');
    }

    public function updateAssociatedExpenses(Request $request, string $id){
        $gasto = AssociatedExpenses::find($id);


        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'received_date' => 'nullable|date',
            'reference' => 'required|string|max:255',
            'date' => 'nullable|date',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'purchase_order_id' => 'required|integer|exists:purchase_order,id',
            'state' => 'required|string|max:255',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'aceptado_gestor' => 'nullable|boolean',
            'iva' => 'nullable',
            'categoria_id' => 'nullable',
            'total' => 'nullable',


        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'purchase_order_id.required' => 'El ID de la orden de compra es obligatorio.',
            'purchase_order_id.integer' => 'El ID de la orden de compra debe ser un número entero.',
            'purchase_order_id.exists' => 'El ID de la orden de compra proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'aceptado_gestor.boolean' => 'El campo aceptado gestor debe ser verdadero o falso.',
        ]);

        $validated['total'] = number_format(($validated['quantity']  * $validated['iva'] / 100) + $validated['quantity'], 2, '.', '');
        // Actualizar el gasto asociado con los datos validados
        $gasto->update($validated);

        // Manejar la carga de archivos
        if ($request->hasFile('documents') && $request->file('documents')->isValid()) {
            $path = $request->file('documents')->store('documents', 'public');
            $gasto->documents = $path;
        }

        // Guardar los cambios
        $gasto->save();

        // Redireccionar con mensaje de éxito
        return redirect()->route('gasto-asociados.index')->with('success', 'Gasto asociado actualizado exitosamente.');
    }

    //destroy
    public function destroyTraspasos(Request $request){
        $id = $request->id;
        if ($id != null) {
            $traspaso = Traspaso::find($id);
            if ($traspaso != null) {
                $traspaso->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El traspaso fue borrado con éxito."
                ], 201);
            }
        }
    }

    public function destroyIngresos(Request $request){
         $id = $request->id;
        if ($id != null) {
            $gasto = Ingreso::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El ingreso fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el ingreso."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function destroyGastos(Request $request){
        $id = $request->id;
        if ($id != null) {
            $gasto = Gasto::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El gasto fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el gasto."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }

    }

    public function destroyAssociatedExpenses(Request $request){
        $id = $request->id;
        if ($id != null) {
            $gasto = AssociatedExpenses::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El gasto fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el gasto."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function destroyUnclassifiedExpensese(Request $request){
        $id = $request->id;
        if ($id != null) {
            $gasto = UnclassifiedExpenses::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El gasto fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el gasto."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function showIngresos(){
        return view('tesoreria.ingresos.index');
    }
}
