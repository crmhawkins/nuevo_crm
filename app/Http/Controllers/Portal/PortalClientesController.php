<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalClientesController extends Controller
{
    public function dashboard(Request $request){
        // var_dump($request->all());
        return view('portal.dashboard');
    }
    public function presupuestos(Request $request){
        // var_dump($request->all());
        return view('portal.presupuestos');
    }
    public function facturas(Request $request){
        // var_dump($request->all());
        return view('portal.facturas');
    }
}
