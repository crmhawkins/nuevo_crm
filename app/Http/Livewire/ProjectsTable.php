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
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto

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
        $query = Project::when($this->buscar, function ($query) {
                    $query->where('name', 'like', '%' . $this->buscar . '%')
                          ->orWhere('description', 'like', '%' . $this->buscar . '%')
                          ->orWhere('notes', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('admin_user_id', $this->selectedGestor);
                }); // Obtiene todos los registros sin paginación

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->projects = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedGestor') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

}
