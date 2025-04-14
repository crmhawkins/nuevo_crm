<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PortalPurchaseDetail;
use App\Models\Projects\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PortalCompraWebs extends Controller
{
    public function generateForm(Request $request) {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }

        $type = $request->type ?? session('form_type');
        $template = $request->structure ?? session('form_structure');

        if (!$type || !$template) {
            return redirect()->route('portal.dashboard')->with('error_message', 'Faltan datos para generar el formulario.');
        }

        session()->forget(['form_type', 'form_structure']);

        $purchase = Purchase::firstOrCreate(
            [
                'client_id' => $cliente->id,
                'status' => 'pendiente',
                'purchase_type' => $type,
            ],
            [
                'template' => $template,
            ]
        );

        $id = $purchase->id;

        return view('portal.formulario-compra', compact('cliente', 'type', 'template', 'id'));
    }

    public function generateFormGet() {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }
        return view('portal.checkout-estructura', compact('cliente'))->with('error_message', 'Error al enviar el formulario.');
    }

    public function showPurchases() {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }

        $compras = Purchase::where('client_id', $cliente->id)
        ->where(function ($query) {
            $query->where('status', 'pagado')
                  ->orWhere('status', 'procesando')
                  ->orWhere('status', 'completado');
        })
        ->get();
            return view('portal.compras', compact('cliente', 'compras'));
    }

    public function storeForm(Request $request) {
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }
    
        // Definir reglas de validación
        $rules = [
            'purchase_id' => 'required|int',
            'marca' => 'required|string|max:22',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'historia' => 'required|string',
            'servicios' => 'required|string',
            'redes' => 'nullable|string',
            'politica' => 'nullable|array',  
            'politica.*' => 'file|mimes:pdf,docx,txt,zip,rar|max:51200', 
        ];
    
        // Ejecutar validador
        $validator = Validator::make($request->all(), $rules);
        $type = $request->type;
        if ($validator->fails()) {
            return redirect()->route('portal.selectStructure', compact('cliente', 'type'))
                ->withErrors($validator)
                ->with('error_message', 'Por favor, corrige los errores del formulario.')
                ->withInput();
        }
    
        // Verificar que la compra sea valida
        $id = (int) $request->input('purchase_id');
        $purchase = Purchase::where('client_id', $cliente->id)
            ->where('payment_status', 'pendiente')
            ->where('id', $id)
            ->first();
    
        if (!$purchase) {
            return redirect()->route('portal.dashboard')->with('error_message', 'Error al enviar el formulario.');
        }
    
        $type = $request->type;
        $purchase_id = $purchase->id;
    
        // Guardar los detalles de la compra
        $purchaseData = $request->except(['politica']);
        $purchaseDetail = PortalPurchaseDetail::where('purchase_id', $purchase->id)->first();
    
        if(!$purchaseDetail) {
            $purchaseDetail = PortalPurchaseDetail::create($purchaseData);
        } else {
            return view('portal.dominios-compra', compact('purchase_id', 'cliente'));
        }
    
        $archivos = [];
        if ($request->hasFile('politica')) {
            $files = $request->file('politica');
            
            foreach ($files as $file) {
                $filePath = $file->store('uploads', 'public'); 
                $archivos[] = $filePath; 
            }
    
            $archivos = implode(',', $archivos);
    
            $purchaseDetail->politica = $archivos;
            $purchaseDetail->save();
        }
    
        // Actualizar estado de la compra
        $purchase->status = 'enviado';
        $purchase->save();
    
        session()->flash('purchase_id', $id);
        return redirect()->route('portal.dominiosCheckout');
    }
    
    


    public function redirectUrl($url) {
        $client = session('cliente');
        if (!$client) {
            return redirect()->route('portal.login');
        }

        $redirects = [
            "ecommerce1" => "https://www.google.com",
            "ecommerce2" => "https://www.youtube.com",
            "ecommerce3" => "https://www.hawkins.es"
        ];

        return redirect($redirects[$url] ?? '/portal/dashboard');
    }

    public function dominiosCheckout() {
        $cliente = session('cliente');
        if (!$cliente) {
            return redirect()->route('portal.login');
        }

        $purchase_id = session('purchase_id');
        return view('portal.dominios-compra', compact('cliente', 'purchase_id'));
    }


    public function dominiosStore(Request $request) {
        $cliente = session('cliente');
        if (!$cliente) {
            return redirect()->route('portal.login');
        }
    
        $purchase_id = $request->purchase_id;
        $purchase = Purchase::where('id', $purchase_id)
            ->where('client_id', $cliente->id)
            ->where('payment_status', 'pendiente')
            ->first();
    
        if (!$purchase) {
            return redirect()->route('portal.checkout')->with('error_message', 'Compra no encontrada.');
        }
    
        $purchaseDetail = PortalPurchaseDetail::where('purchase_id', $purchase_id)
            ->whereNull('dominio')
            ->first();
    
        if (!$purchaseDetail) {
            $purchaseDetailExist = PortalPurchaseDetail::where('purchase_id', $purchase_id)->first();
            if (!$purchaseDetailExist) {
                return redirect()->route('portal.dashboard', compact('cliente'))
                    ->with('error_message', 'Ha ocurrido un error inesperado. ' . $purchase_id);
            }
            session()->flash('purchase', $purchase);
            return redirect()->route('portal.checkout');
        }
    
        $price = 190;
        $iva = $price * 0.21;
        $price = $price + $iva;
        $purchase->amount = $price;
        $purchase->save();
    
        $dominio = $request->input('dominio');
        $dominioExterno = $request->input('dominio_externo');
        $hosting = $request->input('hosting');
        $archivos = [];
    
        if ($request->hasFile('archivo')) {
            $files = $request->file('archivo');
            
            foreach ($files as $file) {
                $archivoPath = $file->store('uploads', 'public');
                $archivos[] = $archivoPath;
            }
        }
    
        $archivos = implode(',', $archivos);
    
        $purchaseDetail->update([
            'dominio' => $dominio === 'si' ? $request->input('nombre_dominio') : null,
            'dominio_externo' => $dominio === 'no' ? $dominioExterno : null,
            'hosting' => $hosting,
            'imagenes' => $archivos,  
        ]);
    
        session()->flash('purchase', $purchase);
        return redirect()->route('portal.checkout');
    }


    
}
