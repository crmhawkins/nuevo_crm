<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CategoriaGastosAsociados;
use Illuminate\Http\Request;

class CategoriaAsociadosController extends Controller
{
    public function index() {

        return view('tesoreria.gastos-asociados-categories.index');
    }

    public function create(){
        return view('tesoreria.gastos-asociados-categories.create');
    }

    public function store(Request $request){
        $rules = [
            'nombre' => 'required|string|max:255',
        ];

        // Validar los datos del formulario
        $validatedData = $request->validate($rules);
        $banco = CategoriaGastosAsociados::create($validatedData);

        return redirect()->route('categorias-gastos-asociados.index')->with('status', 'Categoria de gastos asociado creado con éxito!');

    }
    public function edit(CategoriaGastosAsociados $categoria){

        return view('tesoreria.gastos-asociados-categories.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaGastosAsociados $categoria){
        $rules = [
            'nombre' => 'required|string|max:255',
        ];

        // Validar los datos del formulario
        $validatedData = $request->validate($rules);
        $categoria->update([
            'nombre' => $validatedData['nombre']
        ]);

        return redirect()->route('categorias-gastos-asociados.index')->with('status', 'Categoria de gastos asociado actualizado con éxito!');

    }
    public function destroy(Request $request){
        $id = $request->id;
        if ($id != null) {
            $categoria = CategoriaGastosAsociados::find($id);
            if ($categoria != null) {
                $categoria->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "Categoria de gastos asociado eliminada con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra la categoria de gastos asociado."
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
