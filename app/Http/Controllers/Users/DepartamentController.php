<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\UserDepartament;
use Illuminate\Http\Request;

class DepartamentController extends Controller
{
    public function index()
    {
        $departments = UserDepartament::paginate(10);
        return view('departaments.index', compact('departments'));
    }


    public function create()
    {
        return view('departaments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = new UserDepartament();
        $department->name = $request->name;
        $department->save();

        return redirect()->route('departamento.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El departamento se creó correctamente'
        ]);
    }

    public function edit($id)
    {
        $department = UserDepartament::findOrFail($id);
        return view('departaments.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = UserDepartament::findOrFail($id);
        $department->name = $request->name;
        $department->save();

        return redirect()->route('departamento.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El departamento se actualizó correctamente'
        ]);
    }

    public function destroy(Request $request)
    {
        $department = UserDepartament::findOrFail($request->id);

        if (!$department) {
            return response()->json([
                'status' => false,
                'mensaje' => 'Error en el servidor, inténtelo más tarde.'
            ]);
        }

        $department->delete();

        return response()->json([
            'status' => true,
            'mensaje' => 'El departamento fue borrado correctamente'
        ]);
    }
}
