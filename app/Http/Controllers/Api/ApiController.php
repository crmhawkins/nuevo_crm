<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitDigital;


class ApiController extends Controller
{
    public function getayudas(){
        $kitDigitals = KitDigital::where('estado',18)->where(function($query) {
            $query->where('enviado', '!=', 1)
                  ->orWhereNull('enviado');
        })->get();

        return $kitDigitals;

    }
    public function updateAyudas($id){
        $kitDigital = KitDigital::find($id);
        $kitDigital->enviado = 1;
        $kitDigital->save();

        return response()->json(['success' => $id]);
    }

}
