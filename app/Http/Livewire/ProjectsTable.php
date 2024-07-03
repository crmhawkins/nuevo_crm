<?php

namespace App\Http\Livewire;

use App\Models\Projects\Project;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsTable extends Component
{
    use WithPagination;

    public $gestores;
    public $buscar;
    public $selectedGestor = '';
    public $perPage = 10;

    protected $projects; // Propiedad protegida para las Campañas

    public function mount(){
        $this->gestores = User::where('access_level_id', 4)->get();
    }

    public function render()
    {
        $this->actualizarProjects(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.projects-table', [
            'projects' => $this->projects
        ]);
    }

    protected function actualizarProjects()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        if ($this->perPage === 'all') {
            $this->projects = Project::when($this->buscar, function ($query) {
                    $query->where('name', 'like', '%' . $this->buscar . '%')
                          ->orWhere('description', 'like', '%' . $this->buscar . '%')
                          ->orWhere('notes', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('admin_user_id', $this->selectedGestor);
                })
                ->get(); // Obtiene todos los registros sin paginación
        } else {
            // Usa paginación con la cantidad especificada por $this->perPage
            $this->projects = Project::when($this->buscar, function ($query) {
                    $query->where('name', 'like', '%' . $this->buscar . '%')
                          ->orWhere('description', 'like', '%' . $this->buscar . '%')
                          ->orWhere('notes', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('admin_user_id', $this->selectedGestor);
                })
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10); // Se asegura de que $this->perPage sea numérico
        }
    }

    public function aplicarFiltro()
    {
        $this->actualizarProjects(); // Luego actualizas la lista de usuarios basada en los filtros
    }

    public function getProjects()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->projects;
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedGestor') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

}
