<?php

namespace App\Http\Controllers\Bancos;

use App\Http\Controllers\Controller;
use App\Models\Other\BankAccounts;
use Illuminate\Http\Request;

class BancosController extends Controller
{


    public function store(Request $request){
        $rules = [
            'nombre' => 'required|string|max:255',
        ];

        // Validar los datos del formulario
        $validatedData = $request->validate($rules);
        $banco = BankAccounts::create($validatedData);

        return redirect()->back()->with('status', 'Banco creado con éxito!');

    }

    public function update(Request $request, BankAccounts $banco){
        $rules = [
            'nombre' => 'required|string|max:255',
        ];

        // Validar los datos del formulario
        $validatedData = $request->validate($rules);
        $banco->update([
            'nombre' => $validatedData['nombre']
        ]);

        return redirect()->back()->with('status', 'Banco actualizado con éxito!');

    }

    public function destroy(BankAccounts $banco){
        $banco->delete();

        return redirect()->back()->with('status', 'Banco eliminado con éxito!');
    }
}
