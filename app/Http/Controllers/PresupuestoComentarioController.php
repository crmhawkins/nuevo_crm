<?php

namespace App\Http\Controllers;

use App\Models\PresupuestoComentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresupuestoComentarioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'presupuesto_id' => 'required|exists:budgets,id',
            'comentario' => 'required|string|max:1000',
        ]);

        PresupuestoComentario::create([
            'presupuesto_id' => $request->presupuesto_id,
            'user_id' => Auth::id(),
            'comentario' => $request->comentario,
        ]);

        return back()->with('success', 'Comentario añadido');
    }

    public function destroy($id)
{
    $comentario = PresupuestoComentario::findOrFail($id);

    // Opcional: asegurarte que solo el autor o admin pueda eliminar
    if (Auth::id() !== $comentario->user_id && !Auth::user()->is_admin) {
        abort(403);
    }

    $comentario->delete();

    return back()->with('success', 'Comentario eliminado correctamente.');
}


}
