<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\Services\ServiceCategories;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class TasksAsignarTable extends Component
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
    protected $tasks;

    public function mount(){
        $this->categorias = ServiceCategories::all();
        $this->clientes = Client::All();
        $this->empleados = User::all();
        $this->gestores = User::all();
    }

    public function render()
    {
        $this->actualizartareas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.tasks-asignar-table', [
            'tareas' => $this->tasks
        ]);
    }


    protected function actualizartareas(){
        if ($this->perPage === 'all') {
            $this->tasks = Task::where('split_master_task_id',null)->where('duplicated',0)
            ->when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('description', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCategoria, function ($query) {
                    $query->whereHas('presupuestoConcepto', function ($query) {
                        $query->where('services_category_id', $this->selectedCategoria);
                    });
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->whereHas('presupuesto', function ($query) {
                        $query->where('client_id', $this->selectedCliente);
                    });
                })
                ->when($this->selectedEmpleado, function ($query) {
                    $query->where('admin_user_id', $this->selectedEmpleado);
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('gestor_id', $this->selectedGestor);
                })
                ->get();
        } else {
            $this->tasks = Task::where('split_master_task_id',null)->where('duplicated',0)
            ->when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('description', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCategoria, function ($query) {
                    $query->whereHas('presupuestoConcepto', function ($query) {
                        $query->where('services_category_id', $this->selectedCategoria);
                    });
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->whereHas('presupuesto', function ($query) {
                        $query->where('client_id', $this->selectedCliente);
                    });
                })
                ->when($this->selectedEmpleado, function ($query) {
                    $query->where('admin_user_id', $this->selectedEmpleado);
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('gestor_id', $this->selectedGestor);
                })
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
        }
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
