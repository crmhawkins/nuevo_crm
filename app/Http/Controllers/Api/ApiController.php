<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitDigital;


class ApiController extends Controller
{
    public function getayudas(){
        $kitDigitals = KitDigital::all();

        return $kitDigitals;

    }

}
