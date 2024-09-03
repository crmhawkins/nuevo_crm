<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\Services\ServiceCategories;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use App\Models\Users\UserDepartament;
use Livewire\Component;
use Livewire\WithPagination;

class TasksTable extends Component
{
    use WithPagination;

    public $categorias;
    public $clientes;
    public $empleados;
    public $gestores;
    public $departamentos;
    public $buscar;
    public $selectedCategoria = '';
    public $selectedCliente = '';
    public $selectedEmpleado = '';
    public $selectedGestor = '';
    public $selectedDepartamento = '';
    public $perPage = 10;
    public $sortColumn = 'title'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto
    protected $tasks;

    public function mount(){
        $this->categorias = ServiceCategories::all();
        $this->clientes = Client::All();
        $this->empleados = User::all();
        $this->gestores = User::all();
        $this->departamentos = UserDepartament::all();
    }

    public function render()
    {
        $this->actualizartareas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.tasks-table', [
            'tareas' => $this->tasks
        ]);
    }


    protected function actualizartareas(){
        $query = Task::when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('description', 'like', '%' . $this->buscar . '%');
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
                ->leftJoin('admin_users as gestor', 'tasks.gestor_id', '=', 'gestor.id')
                ->leftJoin('admin_users as empleado', 'tasks.admin_user_id', '=', 'empleado.id')
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
        if (in_array($propertyName, ['buscar', 'selectedCategoria', 'selectedCliente', 'selectedGestor','selectedEmpleado','selectedDepartamento'])) {
            $this->resetPage();
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

}
