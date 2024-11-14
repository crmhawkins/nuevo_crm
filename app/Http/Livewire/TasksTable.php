<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\Services\ServiceCategories;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskStatus;
use App\Models\Users\User;
use App\Models\Users\UserDepartament;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class TasksTable extends Component
{
    use WithPagination;

    public $categorias;
    public $clientes;
    public $empleados;
    public $gestores;
    public $estados;
    public $departamentos;
    public $buscar;
    public $selectedEstado = '';
    public $selectedCategoria = '';
    public $selectedCliente = '';
    public $selectedEmpleado = '';
    public $selectedGestor = '';
    public $selectedDepartamento = '';
    public $selectedYear;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $tasks;

    public function mount(){
        $this->categorias = ServiceCategories::where('inactive',0)->get();
        $this->clientes = Client::where('is_client',true)->get();
        $this->empleados = User::where('inactive',0)->where('access_level_id', 5)->get();
        $this->gestores = User::where('inactive',0)->where('access_level_id', 4)->get();
        $this->departamentos = UserDepartament::all();
        $this->selectedYear = Carbon::now()->year;
        $this->estados = TaskStatus::all();

    }

    public function render()
    {
        $fechasEstimadas = $this->calcularFechasEstimadasTodasTareas();
        $this->actualizartareas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.tasks-table', [
            'tareas' => $this->tasks,
            'fechasEstimadas' => $fechasEstimadas // Enviamos las fechas estimadas
        ]);
    }


    protected function actualizartareas(){
        $query = Task::when($this->buscar, function ($query) {
                    $query->where('tasks.title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('tasks.description', 'like', '%' . $this->buscar . '%')
                          ->orWhereHas('presupuesto', function($q) {
                            $q->where('budgets.reference', 'like', '%' . $this->buscar . '%');
                        })
                          ->orWhereHas('presupuesto.cliente', function($q) {
                            $q->where('clients.name', 'like', '%' . $this->buscar . '%');
                        })
                        ;
                })
                ->when($this->selectedCategoria, function ($query) {
                    $query->whereHas('presupuestoConcepto', function ($query) {
                        $query->where('budget_concepts.services_category_id', $this->selectedCategoria);
                    });
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->whereHas('presupuesto', function ($query) {
                        $query->where('budgets.client_id', $this->selectedCliente);
                    });
                })
                ->when($this->selectedDepartamento, function ($query) {
                    $query->whereHas('usuario', function ($query) {
                        $query->where('admin_user_department_id', $this->selectedDepartamento);
                    });
                })
                ->when($this->selectedEstado, function ($query) {
                    $query->where('tasks.task_status_id', $this->selectedEstado);
                })
                ->when($this->selectedYear, function ($query) {
                    $query->whereYear('tasks.created_at', $this->selectedYear);
                })
                ->when($this->selectedEmpleado, function ($query) {
                    $query->where('tasks.admin_user_id', $this->selectedEmpleado);
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('tasks.gestor_id', $this->selectedGestor);
                })
                ->leftJoin('budget_concepts', 'tasks.budget_concept_id', '=', 'budget_concepts.id')
                ->leftJoin('priority', 'tasks.priority_id', '=', 'priority.id')
                ->leftJoin('budgets', 'tasks.budget_id', '=', 'budgets.id')
                ->leftJoin('clients', 'budgets.client_id', '=', 'clients.id')
                ->leftJoin('admin_user as gestor', 'tasks.gestor_id', '=', 'gestor.id')
                ->leftJoin('admin_user as empleado', 'tasks.admin_user_id', '=', 'empleado.id')
                ->leftJoin('admin_user_department', 'empleado.admin_user_department_id', '=', 'admin_user_department.id')
                ->select('tasks.*', 'priority.name as prioridad', 'admin_user_department.name as departamento','budget_concepts.title as concept', 'clients.name as cliente', 'gestor.name as gestor','empleado.name as empleado');


        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->tasks = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updating($propertyName)
    {
        if (in_array($propertyName, ['buscar','selectedEstado', 'selectedCategoria', 'selectedCliente', 'selectedGestor','selectedEmpleado','selectedDepartamento'])) {
            $this->resetPage();
        }
    }

    public function updatingPerPage(){
        $this->resetPage();
    }

    public function calcularFechasEstimadasTodasTareas() {
        // Parámetros de la jornada laboral
        $horaInicioJornada = 9 * 3600; // 9 AM en segundos
        $horaFinJornada = 19 * 3600; // 7 PM en segundos
        $horasPorJornada = 8 * 3600; // Jornada laboral de 8 horas en segundos

        // Obtener todas las tareas, ordenadas por usuario, prioridad y fecha de creación
        $tareas = Task::with('usuario') // Asumimos que tienes una relación 'user' en Task
                    ->orderBy('admin_user_id', 'asc') // Ordenamos por usuario
                    ->orderBy('priority_id', 'asc') // Ordenamos por prioridad
                    ->orderBy('created_at', 'asc') // Ordenamos por fecha de creación
                    ->get();

        // Array para almacenar las fechas estimadas de cada tarea
        $fechasEstimadas = [];

        // Inicializa la fecha y hora actual para el primer usuario
        $fechaActual = Carbon::now();

        // Si es fin de semana, avanzar al siguiente día laborable
        if ($fechaActual->isWeekend()) {
            $fechaActual->nextWeekday();
        }

        // Iterar por todas las tareas y calcular la fecha estimada de entrega
        $usuarioActualId = null; // Para saber cuándo cambiamos de usuario
        $horaActual = ($fechaActual->hour * 3600) + ($fechaActual->minute * 60) + $fechaActual->second;

        foreach ($tareas as $tarea) {
            // Si cambiamos de usuario, reseteamos la fecha y hora a la actual
            if ($usuarioActualId !== $tarea->admin_user_id) {
                $usuarioActualId = $tarea->admin_user_id;
                $fechaActual = Carbon::now();
                if ($fechaActual->isWeekend()) {
                    $fechaActual->nextWeekday();
                }
                $horaActual = ($fechaActual->hour * 3600) + ($fechaActual->minute * 60) + $fechaActual->second;
            }

            if (empty($tarea->estimated_time) || empty($tarea->real_time)) {
                continue;
            }

            // Convertir tiempo estimado y real en segundos
            $tiempoEstimado = explode(':', $tarea->estimated_time);
            $tiempoConsumido = explode(':', $tarea->real_time);
            $segundosEstimados = ( intval($tiempoEstimado[0]) * 3600) + (intval($tiempoEstimado[1]) * 60) + intval($tiempoEstimado[2]);
            $segundosConsumidos = (intval($tiempoConsumido[0]) * 3600) + (intval($tiempoConsumido[1]) * 60) + intval($tiempoConsumido[2]);

            // Calcular el tiempo restante de la tarea
            $segundosRestantes = max(0, $segundosEstimados - $segundosConsumidos);

            // Verificar cuánto tiempo queda en la jornada actual
            $segundosHastaFinJornada = $horaFinJornada - $horaActual;

            // Si hay tiempo suficiente en la jornada actual para terminar la tarea
            if ($segundosRestantes <= $segundosHastaFinJornada) {
                // Completa la tarea en la jornada actual
                $fechaActual->addSeconds($segundosRestantes);
            } else {
                // Calcular cuántos días laborales completos se necesitan
                $segundosRestantes -= $segundosHastaFinJornada; // Descontar lo que queda de la jornada actual
                $diasLaboralesNecesarios = (int) floor($segundosRestantes / $horasPorJornada);
                $segundosRestantes = $segundosRestantes % $horasPorJornada; // Tiempo restante en la última jornada

                // Avanzar al siguiente día laborable
                $fechaActual->addDays($diasLaboralesNecesarios)->nextWeekday();

                // Añadir las horas restantes de la última jornada
                $fechaActual->setTimeFromTimeString('09:00:00');
                $fechaActual->addSeconds($segundosRestantes);
            }

            // Guardar la fecha estimada de entrega para esta tarea
            $fechasEstimadas[$tarea->id] = [
                'tarea_id' => $tarea->id,
                'usuario_id' => $tarea->admin_user_id,
                'title' => $tarea->title,
                'fecha_estimada' => $fechaActual->format('d/m/Y')
            ];

            // Actualizar la hora actual para la siguiente tarea
            $horaActual = ($fechaActual->hour * 3600) + ($fechaActual->minute * 60) + $fechaActual->second;

            // Si hemos llegado al final de la jornada laboral, avanzar al siguiente día laborable
            if ($horaActual >= $horaFinJornada) {
                $fechaActual->addDay()->nextWeekday();
                $horaActual = $horaInicioJornada;
            }
        }

        // Devolver todas las fechas estimadas para las tareas
        return $fechasEstimadas;
    }


}
