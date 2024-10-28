<?php

namespace App\Http\Controllers\Bajas;

use App\Http\Controllers\Controller;
use App\Models\Bajas\Baja;
use Illuminate\Http\Request;

class BajaController extends Controller
{
    public function index()
    {
        $bajas = Baja::all();
        return view('bajas.index', compact('bajas'));

    }

    public function create()
    {

    }

    public function edit()
    {

    }

    public function store(Request $request)
    {
        $this->validate($request, []);
    }

    public function update(Request $request)
    {
        $this->validate($request, [

        ]);
    }


}
