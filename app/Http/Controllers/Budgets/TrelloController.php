<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Budgets\Budget;
use App\Models\Budgets\TrelloMeta;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Carbon\Carbon;


class TrelloController extends Controller
{


    public function addTask($budgets)
    {
        foreach ($budgets as $budget) {
            $tasks = Task::where("budget_id", $budget->id)->get();
            if (count($tasks) > 0) {
                foreach ($tasks as $task_single) {
                    $metas = DB::table('meta')->where("tasks_id", $task_single->id)->get();
                    $autor = DB::table('admin_user')->where("id", $task_single->admin_user_id)->get();
                    $userId = Auth::id();
                    $usuario = User::find($userId);

                    foreach($metas as $meta){
                        $userMeta = User::find($meta->admin_user_id);
                        $meta->usuario = $userMeta;
                    }

                    $task_single['metas'] = $metas;
                    $task_single['userName'] = $autor;
                    $task_single['usuario'] =  $usuario;
                }
            }
            $budget['task'] = $tasks;

            $budgetDescripcion = TrelloMeta::where('budget_id', $budget->id)->get();
            if (count($budgetDescripcion) > 0) {

                $budget['budgetDescripcion'] = $budgetDescripcion;
            } else {
                $budget['budgetDescripcion'] = '';
            }
        };

        return $budgets;
    }

    /**
     * Mostrar todos los presupuestos
     *
     * @return \Illuminate\Http\Response
     */
    public function actualizarDom()
    {
        function cmp($a, $b)
        {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };

        $userId = Auth::id();
        $usuario = User::find($userId);


        $budgetsStatus2 = Budget::where('budget_status_id', 2)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus3 = Budget::where('budget_status_id', 3)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus5 = Budget::where('budget_status_id', 5)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus6 = Budget::where('budget_status_id', 6)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus7 = Budget::where('budget_status_id', 7)->where('admin_user_id', $usuario->id)->get();


        $status2 = $this->addTask($budgetsStatus2);
        $status3 = $this->addTask($budgetsStatus3);
        $status5 = $this->addTask($budgetsStatus5);

        $budgetsAcceptS = [];

        foreach ($status2 as $status) {
            array_push($budgetsAcceptS, $status);
        }
        foreach ($status3 as $status) {
            array_push($budgetsAcceptS, $status);
        }
        foreach ($status5 as $status) {
            array_push($budgetsAcceptS, $status);
        }



        $metasTrello = DB::table('trello_config_user')->where('admin_user_id', $userId)->first();
        if($metasTrello != null){
           if ($metasTrello->order_column) {
            # code...
            $order = json_decode($metasTrello->order_column);
            $budgetsAccept = $this->orderArray($budgetsAcceptS, $order);
            // $budgetsAccept = $budgetsAcceptS;

            }
        }else {
            $budgetsAccept=$budgetsAcceptS;
        }



        return $budgetsAccept;
    }


    public function index()
    {
        function cmp($a, $b)
        {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };

        $userId = Auth::id();
        $usuario = User::find($userId);


        $budgetsStatus2 = Budget::where('budget_status_id', 2)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus3 = Budget::where('budget_status_id', 3)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus5 = Budget::where('budget_status_id', 5)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus6 = Budget::where('budget_status_id', 6)->where('admin_user_id', $usuario->id)->get();
        $budgetsStatus7 = Budget::where('budget_status_id', 7)->where('admin_user_id', $usuario->id)->get();


        $status2 = $this->addTask($budgetsStatus2);
        $status3 = $this->addTask($budgetsStatus3);
        $status5 = $this->addTask($budgetsStatus5);

        $budgetsAcceptS = [];

        foreach ($status2 as $status) {
            array_push($budgetsAcceptS, $status);
        }
        foreach ($status3 as $status) {
            array_push($budgetsAcceptS, $status);
        }
        foreach ($status5 as $status) {
            array_push($budgetsAcceptS, $status);
        }



        $metasTrello = DB::table('trello_config_user')->where('admin_user_id', $userId)->first();
        if($metasTrello != null){
           if ($metasTrello->order_column) {
            # code...
            $order = json_decode($metasTrello->order_column);
            $budgetsAccept = $this->orderArray($budgetsAcceptS, $order);
            // $budgetsAccept = $budgetsAcceptS;

        }
        }else{
            $budgetsAccept=$budgetsAcceptS;

        }

        return view('admin.budgets.management', compact('budgetsAccept', 'usuario'));
    }

    public function orderArray($array, $orders)
    {
        $budget = $array;
        $newArrayBudget = [];
        foreach ($orders as $order) {
            for ($i = 0; $i < count($budget); $i++) {
                if ($budget[$i]->id == $order->id) {


                    array_push($newArrayBudget, $budget[$i]);
                }
            }
        }

        return $newArrayBudget;
    }


    public function changeOrder(Request $request)
    {
        $userId = Auth::id();
        $metasTrello = DB::table('trello_config_user')->where('admin_user_id', $userId)->first();
        $dataRequest = json_encode($request->order);

        if ($metasTrello == null) {


            $data = [
                'admin_user_id' => $userId,
                'order_column' => $dataRequest
            ];

            $crearTrello = DB::table('trello_config_user')->insert($data);
        } else {

            $metasTrello->admin_user_id = $userId;
            $metasTrello->order_column = $dataRequest;

            DB::table('trello_config_user')->where('admin_user_id', $userId)->update(['admin_user_id' => $userId, 'order_column' => $dataRequest]);
        }

        $response = json_encode(array(
            "estado" => $metasTrello
        ));
        return $response;
    }
    public function setNuevaNota(Request $request)
    {

        $tareaId = $request->tareaId;
        $descripcion = $request->descripcion;
        $date = Carbon::now();
        $user = User::find($request->user);
        $gestor = User::find($request->gestor);
        $userName = $user->name;

        try {

            DB::table('meta')->insert([
                'tasks_id' => $tareaId,
                'admin_user_id' => $user->id,
                'gestor_id' => $gestor->id,
                'description' => htmlspecialchars($descripcion),
                'created_at' => $date
            ]);

            $response = json_encode(array(
                "estado" => "OK"
            ));
        } catch (\Throwable $th) {

            $response = json_encode(array(
                "estado" => $th
            ));
        }
        return $response;
    }

    public function addDescripcion(Request $request)
    {
        try {
            $budgetId = $request->id;
            $descripcion = $request->descripcion;
            $date = Carbon::now();
            // $gestor = User::find($request->gestor);
            // $userName = $user->name;
            $userId = Auth::id();

            $data = [
                'admin_user_id' => $userId,
                'budget_id' => $budgetId,
                'descripcion' => $descripcion,
                'actividad' => null,
                'created_at' => $date
            ];

            DB::table('trello_meta')->insert($data);

            $response = json_encode(array(
                "estado" => "OK"
            ));

            return $response;
        } catch (\Throwable $th) {

            $response = json_encode(array(
                "estado" => $th
            ));

            return $response;
        }
    }
    public function addActividad(Request $request)
    {
    }

}
