<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Services\ServiceCategories;
use Illuminate\Http\Request;

class ServicesCategoriesController extends Controller
{
    public function index()
    {
        $servicios = ServiceCategories::paginate(2);
        return view('services-categories.index', compact('servicios'));
    }

    public function create() {
        return view('services-categories.create');
    }

    public function store(Request $request) {
        // Validamos los campos
        $data = $this->validate($request, [
            'name' => 'required|max:255',
            'terms' => 'required',
            'type' => 'required',
        ], [
            'name.required' => 'El titulo es requerido para continuar',
            'name.max' => 'El titulo no pueder tener mas de 255 caracteres',
            'terms.required' => 'El concepto es requerido para continuar',
            'terms.max' => 'El concepto no pueder tener mas de 255 caracteres',
            'type.required' => 'El precio es requerido para continuar',
        ]);

        $categoriaCreada = ServiceCategories::create($data);

        if($categoriaCreada){
            return redirect()->route('serviciosCategoria.edit', $categoriaCreada->id)->with('toast', [
                    'icon' => 'success',
                    'mensaje' => 'El categoria creada con exito'
            ]);
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Error en la creacion de la categoria'
            ]);
        }

    }

    public function edit(string $id){
        $categoria = ServiceCategories::find($id);
        if (!$categoria) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'La categoria no existe'
            ]);
            return redirect()->route('serviciosCategoria.index');
        }
        return view('services-categories.edit', compact('categoria'));
    }

    public function update(string $id ,Request $request) {
        $categoria = ServiceCategories::find($id);
        $data = $this->validate($request, [
            'name' => 'required|max:255',
            'terms' => 'required',
            'type' => 'required',
            'inactive' => 'nullable'
        ], [
            'name.required' => 'El titulo es requerido para continuar',
            'name.max' => 'El titulo no pueder tener mas de 255 caracteres',
            'terms.required' => 'El concepto es requerido para continuar',
            'terms.max' => 'El concepto no pueder tener mas de 255 caracteres',
            'type.required' => 'El precio es requerido para continuar',
        ]);

        $categoriaCreada = $categoria->update($data);

        return redirect()->route('serviciosCategoria.index')->with('toast', [
                'icon' => 'success',
                'mensaje' => 'El servicio actualizado con exito'
        ]);
    }

    public function destroy(Request $request) {
        $servicio = ServiceCategories::find($request->id);

        if (!$servicio) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $servicio->delete();

        return response()->json([
            'error' => false,
            'mensaje' => 'La categoria de servicio fue borrada correctamente'
        ]);
    }


}
