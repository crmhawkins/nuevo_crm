<?php

namespace App\Http\Controllers\Payment_method;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethods\PaymentMethod;
use Illuminate\Http\Request;

class FormasDePagoController extends Controller
{
    public function store(Request $request){
        $nombre = $request->nombre;
        $formaNueva = PaymentMethod::create([
            'nombre' => $nombre
        ]);

        //Alert::toast('Creado', 'success');
        return redirect()->route('configuracion.index');

    }

    public function update($id, Request $request){
        $formaDePago = PaymentMethod::find($id);
        $nombre = $request->nombre;

        $formaDePago->nombre = $nombre;
        $formaDePago->save();
        return true;
    }

    public function delete($id){
        $formaDePago = PaymentMethod::find($id);

        if($formaDePago){
            $formaDePago->delete();
            return redirect(route('configuracion.index'));
        }

    }
}
