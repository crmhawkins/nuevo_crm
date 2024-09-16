<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Countries\Country;
use App\Models\Suppliers\Supplier;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    /**
     * Obtener los proveedores y devolver un JSON
     *
     * @param Supplier $supplier
     *
     * @return \Illuminate\Http\Response
     */
    public function getSupplier(Supplier $supplier)
    {
        $getSupplier= Supplier::where('id',$supplier->id)->get()->first();

        return response()->json(array('success' => true, 'getSupplier' => $getSupplier));
    }

      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedores = Supplier::all();
        return view('suppliers.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        return view('suppliers.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los campos
        $data = $this->validate($request, [
            'name' => 'nullable',
            'dni' => 'nullable',
            'cif' => 'nullable',
            'email' => 'nullable',
            'country' => 'nullable',
            'city' => 'nullable',
            'province' => 'nullable',
            'address' => 'nullable',
            'zipcode' => 'nullable',
            'work_activity' => 'nullable',
            'phone' => 'nullable',
            'fax' => 'nullable',
            'web' => 'nullable',
            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'linkedin' => 'nullable',
            'instagram' => 'nullable',
            'pinterest' => 'nullable',
            'note' => 'nullable',
        ]);

        $proveedor = Supplier::create($data);


        return redirect()->route('proveedores.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El proveedor se creo correctamente'
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $proveedor = Supplier::find($id);
        $countries = Country::all();
        return view('suppliers.edit', compact('proveedor', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validamos los campos
        $data = $this->validate($request, [
            'name' => 'nullable',
            'dni' => 'nullable',
            'cif' => 'nullable',
            'email' => 'nullable',
            'country' => 'nullable',
            'city' => 'nullable',
            'province' => 'nullable',
            'address' => 'nullable',
            'zipcode' => 'nullable',
            'work_activity' => 'nullable',
            'phone' => 'nullable',
            'fax' => 'nullable',
            'web' => 'nullable',
            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'linkedin' => 'nullable',
            'instagram' => 'nullable',
            'pinterest' => 'nullable',
            'note' => 'nullable',
        ]);

        $proveedor = Supplier::findOrFail($id);
        $proveedor->update($data);


        return redirect()->route('proveedores.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El proveedor se actualizó correctamente'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $proveedor = Supplier::find($request->id);

        if (!$proveedor) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $proveedor->delete();
        return response()->json([
            'error' => false,
            'mensaje' => 'El proveedor fue borrado correctamente'
        ]);
    }

}
