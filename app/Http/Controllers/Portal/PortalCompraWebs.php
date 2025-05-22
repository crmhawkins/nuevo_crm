<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetConceptType;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\PortalCoupon;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PortalPurchaseDetail;
use App\Models\Projects\Project;
use App\Models\TempUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\Coupon;

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
        session(['purchase' => $purchase]);
        $id = $purchase->id;

        if ($cliente->id == 320574) {
            $tempuser = session('tempuser');
            $tempuser-> compra = $id;
            $tempuser->save();
            return view('portal.temp.tempformulario-compra', compact('cliente', 'type', 'template', 'id'));
        }


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
            'politica' => 'nullable|string',  // Aceptar texto para 'politica', no archivos
            'color_principal' => 'required|string',
            'color_secundario' => 'required|string'
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

        // Incluir el campo 'politica' junto con los demás datos
        $purchaseData = $request->all();

        // Crear el registro de detalles de compra y guardar el campo 'politica' directamente
        $purchaseDetail = PortalPurchaseDetail::create($purchaseData);

        // Actualizar estado de la compra
        $purchase->status = 'pendiente';
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

        if ($cliente->id == 320574) {
            return view('portal.temp.tempdominios-compra', compact('cliente', 'purchase_id'));
        } else {
            return view('portal.dominios-compra', compact('cliente', 'purchase_id'));
        }
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
            return redirect()->route('portal.dashboard')->with('error_message', 'Error al procesar la compra.');
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
            session(['purchase' => $purchase]); // guarda la sesión de forma persistente
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

        session(['purchase' => $purchase]); // guarda la sesión de forma persistente
        return redirect()->route('portal.checkout');
    }


    // Generar cupones y usuarios temporales
    public function generarUserView() {
        $admin = session('admin');
        if (!$admin) {
            return redirect()->route('portal.loginAdmin');
        } else {
            return view('portal.generaruser');
        }
    }

    public function generarUser() {
        $admin = session('admin');
        if (!$admin) {
            return redirect()->route('portal.loginAdmin');
        }
        do {
            $userNumber = random_int(100000, 999999);
        } while (TempUser::where('user', $userNumber)->exists());

        $pin = random_int(100000, 999999);

        TempUser::create(['user' => $userNumber, 'password' => $pin]);

        return view('portal.generaruser', compact('userNumber', 'pin'));
    }

    public function generarCuponView() {
        $admin = session('admin');
        if (!$admin) {
            return redirect()->route('portal.loginAdminGet');
        } else {
            return view('portal.generarcupon');
        }
    }

    public function generarCupon(Request $request) {
        $discount = $request->discount;
        $admin = session('admin');
        if (!$admin) {
            return redirect()->route('portal.loginAdminGet');
        }
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $cupon = substr(str_shuffle($characters), 0, 7);
        } while (PortalCoupon::where('id', $cupon)->exists());

        PortalCoupon::create(['id' => $cupon, 'discount' => $discount]);

        return view('portal.generarcupon', compact('cupon', 'discount'));
    }

    public function loginAdminGet(Request $request) {
        return view('portal.login-admin');
    }

    public function loginAdmin(Request $request) {
        // Obtener el administrador por su nombre de usuario
        $admin = AdminUser::where('username', $request->usuario)->first();

        if (!$admin || !Hash::check($request->pin, $admin->password)) {
            return redirect()->route('portal.loginAdminGet')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Usuario o PIN incorrecto.'
            ]);
        } else {
            session(['admin' => $admin]);
            return redirect()->route('portal.generarUserView');
        }
}

}
