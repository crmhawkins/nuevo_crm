<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalClientesController extends Controller
{
    public function login(Request $request){

        if($request->logout == true){
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('portal.login');
    }

    public function loginPost(Request $request){
        $cliente = Client::where('pin', $request->pin)->first();
        if ($cliente) {
            session(['cliente' => $cliente]);
            return redirect()->route('portal.dashboard');
        }
        return redirect()->route('portal.login');
    }

    public function dashboard(Request $request){
        $cliente = session('cliente');

        if ($cliente) {
            return view('portal.dashboard', compact('cliente'));
        }
        return view('portal.login');
    }
    public function presupuestos(Request $request){
        $cliente = session('cliente');
        if ($cliente) {
            return view('portal.presupuestos',compact('cliente'));
        }
        return view('portal.login');
    }
    public function facturas(Request $request){
        $cliente = session('cliente');
        if ($cliente) {
            return view('portal.facturas',compact('cliente'));
        }
        return view('portal.login');
    }
}
