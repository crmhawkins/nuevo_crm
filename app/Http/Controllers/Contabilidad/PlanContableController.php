<?php

// App\Http\Controllers\PlanContableController.php
namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Accounting\GrupoContable;
use Illuminate\Http\Request;

class PlanContableController extends Controller
{
    public function index()
    {
        $grupos = GrupoContable::with('subGrupos.cuentas.subCuentas.cuentasHijas')->orderBy('numero', 'asc')->get();
        return view('contabilidad.planContable.index', compact('grupos'));
    }
}
