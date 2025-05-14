<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalvineitorController extends Controller
{
    public function index()
    {
        # Retorna 200
        return response()->json([
            'sucess' => 'OK'
        ], 200);
    }
}
