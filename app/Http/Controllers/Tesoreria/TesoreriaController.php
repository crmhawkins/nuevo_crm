<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TesoreriaController extends Controller
{
    public function indexIngresos(){
        return view('tesoreria.ingresos.index');
    }
}
