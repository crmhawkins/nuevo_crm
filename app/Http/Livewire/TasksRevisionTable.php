<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\Services\ServiceCategories;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class TasksRevisionTable extends Component
{
    use WithPagination;

    public $categorias;
    public $clientes;
    public $empleados;
    public $gestores;
    public $buscar;
    public $selectedCategoria = '';
    public $selectedCliente = '';
    public $selectedEmpleado = '';
    public $selectedGestor = '';
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $tasks;

    public function mount(){
        $this->categorias = ServiceCategories::where('inactive',0)->get();
        $this->clientes = Client::All();
        $this->empleados = User::where('inactive',0)->where('access_level_id',5)->get();
        $this->gestores = User::where('inactive',0)->whereIn('access_level_id', [1, 2, 3, 4])->get();
    }

    public function render()
    {
        $this->actualizartareas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.tasks-revision-table', [
            'tareas' => $this->tasks
        ]);
    }


    protected function actualizartareas(){
        $query= Task::where('task_status_id',5)
            ->when($this->buscar, function ($query) {
                    $query->where('tasks.title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('tasks.description', 'like', '%' . $this->buscar . '%');
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
                ->when($this->selectedEmpleado, function ($query) {
                    $query->where('tasks.admin_user_id', $this->selectedEmpleado);
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('tasks.gestor_id', $this->selectedGestor);
                })
                ->join('budget_concepts', 'tasks.budget_concept_id', '=', 'budget_concepts.id')
                ->join('services_categories', 'budget_concepts.services_category_id', '=', 'services_categories.id')
                ->join('budgets', 'tasks.budget_id', '=', 'budgets.id')
                ->join('clients', 'budgets.client_id', '=', 'clients.id')
                ->join('admin_user as gestor', 'tasks.gestor_id', '=', 'gestor.id')
                ->join('admin_user as empleado', 'tasks.admin_user_id', '=', 'empleado.id')
                ->select('tasks.*', 'services_categories.name as categoria_nombre','budget_concepts.title as concept', 'clients.name as cliente', 'gestor.name as gestor','empleado.name as empleado');


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
        if (in_array($propertyName, ['buscar', 'selectedCategoria', 'selectedCliente', 'selectedGestor','selectedEmpleado'])) {
            $this->resetPage();
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

}
