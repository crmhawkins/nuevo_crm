<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CategoriaGastos;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

class CategoriaGastosController extends Controller
{
    public function index() {

        return view('tesoreria.gastos-categories.index');
    }

    public function create(){
        return view('tesoreria.gastos-categories.create');
    }

    public function store(Request $request){
        $rules = [
            'nombre' => 'required|string|max:255',
        ];
        // Validar los datos del formulario
        $validatedData = $request->validate($rules);
        $banco = CategoriaGastos::create($validatedData);

        return redirect()->route('categorias-gastos.index')->with('status', 'Categoria de gasto creado con éxito!');

    }
    public function edit(CategoriaGastos $categoria){

        return view('tesoreria.gastos-categories.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaGastos $categoria){
        $rules = [
            'nombre' => 'required|string|max:255',
        ];

        // Validar los datos del formulario
        $validatedData = $request->validate($rules);
        $categoria->update([
            'nombre' => $validatedData['nombre']
        ]);

        return redirect()->route('categorias-gastos.index')->with('status', 'Categoria de gasto actualizado con éxito!');

    }
    public function destroy(Request $request){

        $id = $request->id;
        if ($id != null) {
            $categoria = CategoriaGastos::find($id);
            if ($categoria != null) {
                $categoria->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "Categoria de gastos eliminada con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra la categoria de gastos ."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }

    }
}
