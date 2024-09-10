<?php

namespace App\Http\Livewire;

use App\Models\Incidence\Incidences;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class IncidencesTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedUser;
    public $selectedAnio;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $incidencias; // Propiedad protegida para las incidencias

    public function mount()
    {
        $this->usuarios = User::all();
    }

    public function render()
    {
        $this->actualizarIncidencias(); // Actualiza las incidencias para reflejar cambios
        return view('livewire.incidences-table', [
            'incidencias' => $this->incidencias
        ]);
    }

    protected function actualizarIncidencias()
    {
        $query = Incidences::when($this->buscar, function ($query) {
            $query->where('titulo', 'like', '%' . $this->buscar . '%');
        })
        ->when($this->selectedUser, function ($query) {
            $query->where('admin_user_id', $this->selectedUser);
        })
        ->when($this->selectedAnio, function ($query) {
            $query->whereYear('created_at', $this->selectedAnio);
        })
        ->when($this->selectedMes, function ($query) {
            $query->whereMonth('created_at', $this->selectedMes);
        });

        $query->orderBy($this->sortColumn, $this->sortDirection);

        $this->incidencias = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if (in_array($propertyName, ['buscar', 'selectedUser', 'selectedAnio', 'selectedMes'])) {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }
}
