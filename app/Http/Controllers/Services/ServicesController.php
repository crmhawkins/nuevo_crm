<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Services\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        $servicios = Service::paginate(2);
        return view('services.index', compact('servicios'));
    }

    public function create() {

    }

    public function store() {

    }

    public function edit() {

    }

    public function update() {

    }

    public function destroy(Request $request) {
        $servicio = Service::find($request->id);

        if (!$servicio) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $servicio->delete();

        return response()->json([
            'error' => false,
            'mensaje' => 'El servicio fue borrado correctamente'
        ]);
    }

    // CATEGORIA DE SERVICIOS
    public function servicesCategories() {

    }

    public function servicesCategoriesCreate() {

    }

    public function servicesCategoriesEdit() {

    }

    public function servicesCategoriesStore() {

    }

    public function servicesCategoriesUpdate() {

    }

    public function servicesCategoriesDestroy() {

    }

}
