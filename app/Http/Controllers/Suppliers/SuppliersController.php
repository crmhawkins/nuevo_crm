<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
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
}
