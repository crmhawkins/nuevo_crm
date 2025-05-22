<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\UserPosition;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = UserPosition::paginate(10);
        return view('positions.index', compact('positions'));
    }


    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $position = new UserPosition();
        $position->name = $request->name;
        $position->save();

        return redirect()->route('cargo.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El puesto se creó correctamente'
        ]);
    }

    public function edit($id)
    {
        $position = UserPosition::findOrFail($id);
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $position = UserPosition::findOrFail($id);
        $position->name = $request->name;
        $position->save();

        return redirect()->route('cargo.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El puesto se actualizó correctamente'
        ]);
    }

    public function destroy(Request $request)
    {
        $position = UserPosition::findOrFail($request->id);

        if (!$position) {
            return response()->json([
                'status' => false,
                'mensaje' => 'Error en el servidor, inténtelo más tarde.'
            ]);
        }

        $position->delete();

        return response()->json([
            'status' => true,
            'mensaje' => 'El puesto fue borrado correctamente'
        ]);
    }
}
