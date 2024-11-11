<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
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
            return view('portal.presupuestos',compact('cliente'));
        }
        return view('portal.login');
    }
    public function facturas(Request $request){
        $cliente = session('cliente');
        if ($cliente) {
            return view('portal.facturas',compact('cliente'));
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


            foreach ($proyectos as $proyecto) {
                $tasks = Task::where('budget_id', $proyecto->id)->whereIn('task_status_id', [1, 2,3])->whereNotNull('split_master_task_id')->get();
                $taskMaestra = Task::where('budget_id', $proyecto->id)->where('split_master_task_id', null)->get();

                foreach ($taskMaestra as $task) {
                    $tiempo =explode(":", $task->total_time_budget);
                    $totalsegundos += $tiempo[0] * 3600 + $tiempo[1] * 60 + $tiempo[2];
                }

                foreach ($tasks as $task) {
                    if ($task->task_status_id == 1 || $task->task_status_id == 2) {
                        $logTasks = LogTasks::where('task_id', $task->id)
                        ->whereNull('deleted_at')
                        ->get();

                        $task['logTasks'] = $logTasks;
                    }
                    $tiemporeal =explode(":", $task->real_time);
                    //dd($tiemporeal);
                    $tiempoGastado += $tiempo[0] * 3600 + $tiempo[1] * 60 + $tiempo[2];

                }

                $proyecto['tasks'] = $tasks;
                array_push($tasksPro, $tasks);
            }
            if (!isset($taskMaestra)) {
                $taskMaestra = null;
            }
           // dd($tiempoGastado,$totalsegundos,$totalsegundos - $tiempoGastado);
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
}
