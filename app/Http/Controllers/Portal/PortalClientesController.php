<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Company\CompanyDetails;
use App\Models\Invoices\Invoice;
use App\Models\Tasks\LogTasks;
use App\Models\Tasks\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalClientesController extends Controller
{
    public function login(Request $request){

        if($request->logout == true){
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('portal.login');
    }

    public function loginPost(Request $request){
        $pin =  $request->pin;
        $usuario = $request->usuario;
        $part = explode('#',$usuario);
        if(empty($part['1'])){
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El usuario no existe'
            ]);
        }
        $cliente = Client::where('pin', $pin)->where('id',$part[1])->first();

        if ($cliente) {
            session(['cliente' => $cliente]);
            return redirect()->route('portal.dashboard');
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'El pin no es correcto'
            ]);
        }
    }

    public function dashboard(Request $request){
        $cliente = session('cliente');

        if ($cliente) {
            return view('portal.dashboard', compact('cliente'));
        }
        return view('portal.login');
    }
    public function presupuestos(Request $request){
        $cliente = session('cliente');
        if ($cliente) {
            $presupuestos = Budget::where('client_id',$cliente->id)->WhereYear('created_at','2024')->WhereMonth('created_at','12')->get();
            return view('portal.presupuestos',compact('cliente','presupuestos'));
        }
        return view('portal.login');
    }
    public function facturas(Request $request){
        $cliente = session('cliente');
        if ($cliente) {
            $facturas = Invoice::where('client_id',$cliente->id)->WhereYear('created_at','2024')->WhereMonth('created_at','12')->get();

            return view('portal.facturas',compact('cliente','facturas'));
        }
        return view('portal.login');
    }
    public function tareasActivas(Request $request){
        $cliente = session('cliente');
        if ($cliente) {
            return view('portal.tareasActivas',compact('cliente'));
        }
        return view('portal.login');
    }

    public function pageTasksViewer(Request $request)
    {
        $cliente = session('cliente');
        if ($cliente) {
            $proyectos = Budget::where('client_id', $cliente->id)
                    ->whereIn('budget_status_id', [3, 6, 7])
                    ->orderBy('id', 'desc')
                    ->get();

            $tasksPro = [];
            $events = [];
            $ids = [];
            $logsArray = [];
            $totalsegundos = 0;
            $tiempoGastado = 0;
            $count = 0;


            foreach ($proyectos as $proyecto) {
                $tasks = Task::where('budget_id', $proyecto->id)->whereIn('task_status_id', [1, 2])->whereNotNull('split_master_task_id')->get();
                $taskMaestra = Task::where('budget_id', $proyecto->id)->whereIn('task_status_id', [1, 2])->where('split_master_task_id', null)->get();

                foreach ($taskMaestra as $task) {
                    $tiempo =explode(":", $task->total_time_budget);
                    $totalsegundos += $tiempo[0] * 3600 + $tiempo[1] * 60 + $tiempo[2];
                }

                foreach ($tasks as $task) {
                    $tiempoEnSegundos = 0;
                    $tiemporeal = explode(":", $task->real_time);
                    $tiempoGastado += ($tiemporeal[0] * 3600) + ($tiemporeal[1] * 60) + $tiemporeal[2];
                }

                $proyecto['tasks'] = $tasks;
                array_push($tasksPro, $tasks);
            }
            if (!isset($taskMaestra)) {
                $taskMaestra = null;
            }

            $tiempoTotalFormato = $this->secondsToTime($totalsegundos);
            $tiempoGastadoFormato = $this->secondsToTime($tiempoGastado);
            $tiempoRestanteFormato = $this->secondsToTime($totalsegundos - $tiempoGastado);
            return view('portal.tareasActivas',compact('ids', 'cliente', 'proyectos', 'tasksPro', 'ids','tiempoTotalFormato','tiempoGastadoFormato','tiempoRestanteFormato'));
        }
        return view('portal.login');
    }


    /**
     * Convertir segundos a formato 00:00:00
     *
     * @param int $seconds
     * @return string
     */
    private function secondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }



    public function changePin(Request $request){
        $cliente = session('cliente');
        if ($cliente) {

            return view('portal.pin',compact('cliente'));
        }
        return view('portal.login');
    }

    public function setPin(Request $request)
    {
        $cliente = session('cliente');
        if ($cliente) {
            // Validación del PIN
            $request->validate([
                'pin' => 'required|size:6|confirmed'
            ], [
                'pin.confirmed' => 'El PIN no coincide. Por favor, inténtelo de nuevo.',
                'pin.required' => 'El campo de PIN es obligatorio.',
                'pin.size' => 'El PIN debe tener exactamente 6 dígitos.'
            ]);

            // Guardar el nuevo PIN
            $cliente->pin = $request->pin;
            $cliente->save();

            // Redirigir con mensaje de éxito
            return redirect()->route('portal.dashboard')->with('toast', [
                'icon' => 'success',
                'mensaje' => 'PIN cambiado con éxito'
            ]);
        }

        return view('portal.login')->with('toast', [
            'icon' => 'error',
            'mensaje' => 'Debe iniciar sesión para cambiar el PIN.'
        ]);
    }

    public function showBudget(Request $request, $id)
    {
        // Verificar que el cliente esté autenticado
        $cliente = session('cliente');
        if (!$cliente) {
            return view('portal.login');
        }
        $empresa = CompanyDetails::find(1);

        // Obtener el presupuesto por ID y asegurar que pertenezca al cliente autenticado
        $budget = Budget::where('id', $id)->where('client_id', $cliente->id)->first();

        if (!$budget) {
            return redirect()->route('portal.presupuestos')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Presupuesto no encontrado o no autorizado.'
            ]);
        }
        // Formatear conceptos del presupuesto si existen
        $concepts = $budget->budgetConcepts()->get()->map(function ($concept) {
            return [
                'title' => $concept->title,
                'units' => $concept->units,
                'unit_price' => $concept->sale_price / $concept->units,
                'subtotal' => $concept->sale_price,
                'discount' => $concept->discount,
                'total' => $concept->total,
            ];
        });

        return view('portal.presupuesto', compact('cliente', 'budget', 'concepts', 'empresa'));
    }
    public function showInvoice(Request $request,$id)
    {
        // Verificar si el cliente ha iniciado sesión
        $cliente = session('cliente');
        if (!$cliente) {
            return redirect()->route('portal.login')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Debe iniciar sesión para ver sus facturas.'
            ]);
        }
        $empresa = CompanyDetails::find(1);

        // Obtener la factura y verificar que pertenezca al cliente actual
        $invoice = Invoice::where('id', $id)->where('client_id', $cliente->id)->first();
        if (!$invoice) {
            return redirect()->route('portal.facturas')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No se encontró la factura solicitada.'
            ]);
        }

        // Formatear los conceptos de la factura
        $invoiceConceptsFormated = $invoice->invoiceConcepts->map(function ($concept) {
            return [
                'title' => $concept->title,
                'description' => explode("\n", $concept->description),
                'units' => $concept->units,
                'price_unit' => number_format($concept->price_unit, 2),
                'subtotal' => number_format($concept->subtotal, 2),
                'discount' => $concept->discount ? number_format($concept->discount, 2) . '%' : null,
                'total' => number_format($concept->total, 2),
            ];
        });

        // Preparar datos adicionales para el resumen de la factura
        $data = [
            'gross' => number_format($invoice->gross, 2),
            'discount' => $invoice->discount ? number_format($invoice->discount, 2) : null,
            'base' => number_format($invoice->base, 2),
            'iva' => number_format($invoice->iva, 2),
            'total' => number_format($invoice->total, 2),
            'iva_percentage' => $invoice->iva_percentage,
            'title' => 'Factura Detallada'
        ];

        // Renderizar la vista con la factura y los conceptos
        return view('portal.factura', compact('cliente','invoice', 'invoiceConceptsFormated', 'data','empresa'));
    }


}
