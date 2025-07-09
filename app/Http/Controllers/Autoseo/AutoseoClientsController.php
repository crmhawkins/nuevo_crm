<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use Illuminate\Http\Request;

class AutoseoClientsController extends Controller
{
    public function index()
    {
        $clients = Autoseo::all();
        return view('autoseo.clients', compact('clients'));
    }
}
