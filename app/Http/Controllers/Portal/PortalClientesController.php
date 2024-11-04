<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalClientesController extends Controller
{
    public function login(Request $request){
        return view('portal.login');
    }

    public function loginPost(Request $request){
        $cliente = Client::where('pin', $request->pin)->first();
        if ($cliente) {
            Auth::login($cliente);
            return redirect()->route('portal.dashboard');
        }
        return redirect()->route('portal.login');
    }

    public function dashboard(Request $request){
        $cliente = Auth::user();

        if ($cliente) {
            return view('portal.dashboard', compact('cliente'));
        }
        return view('portal.login');
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
