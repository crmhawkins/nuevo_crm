<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Iva;
use App\Models\Accounting\LastYearsBalance;
use App\Models\Other\BankAccounts;

class CierreController extends Controller
{

    public function index()
    {
        return view('tesoreria.cierre.index');
    }
    public function create()
    {
        $bankAccounts = BankAccounts::all();
        return view('tesoreria.cierre.create',compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $cierres = $request->cierres;
        foreach ($cierres as $cierre) {
            if(!isset($cierre['year'])){
                continue;
            }
            if(!isset($cierre['valor'])){
                continue;
            }
            $data['year'] = $cierre['year'];
            $data['bank_id'] = $cierre['banco'];
            $data['quantity'] = $cierre['valor'];
            $exist = LastYearsBalance::where('year', $cierre['year'])->where('bank_id', $cierre['banco'])->first();
            if($exist){
                $exist->quantity = $cierre['valor'];
                $exist->save();
            }else{
                $newcierre = LastYearsBalance::create($data);
                $newcierre->save();
            }
        }
        return redirect()->route('cierre.index')->with('toast',[
            'icon' => 'success',
            'mensaje' => 'Cierre creado exitosamente'
        ]);
    }


    public function edit(string $id)
    {
        $iva = Iva::find($id);
        if (!$iva) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El IVA no existe'
            ]);
            return redirect()->route('iva.index');
        }
        return view('tesoreria.iva.edit', compact('iva'));
    }

    public function update(Request $request, string $id)
    {
        $iva = Iva::find($id);
        if (!$iva) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El IVA no existe'
            ]);
            return redirect()->route('iva.index');
        }
        $validated = $this->validate($request, [
            'nombre' => 'required',
            'valor' => 'required',
        ],[
            'nombre.required' => 'El nombre es obligatorio.',
            'valor.required' => 'El valor es obligatorio.',
        ]);
        $ivaUpdated = $iva->update($validated);

        if($ivaUpdated){
            return redirect()->route('iva.index', $iva->id)->with('toast',[
                'icon' => 'success',
                'mensaje' => 'El IVA se actualizo correctamente'
            ]);
        }else{
            return redirect()->back()->with('toast',[
                'icon' => 'error',
                'mensaje' => 'Error al actualizar el IVA'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $iva = Iva::find($request->id);
        if($iva){
            $iva->delete();
            return redirect()->back()->with('toast',[
                'icon' => 'success',
                'mensaje' => 'El IVA se elimino correctamente'
            ]);
        }else{
            return redirect()->back()->with('toast',[
                'icon' => 'error',
                'mensaje' => 'Error al eliminar el IVA'
            ]);
        }
    }
}
