<?php

namespace App\Http\Livewire;

use App\Models\Logs\LogActions;
use App\Models\logs\LogsTipes;
use App\Models\Users\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LogsTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedYear;
    public $tipo;
    public $tipos;
    public $usuarios;
    public $usuario;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto

    protected $logs; // Propiedad protegida para los usuarios

    public function mount(){
        $this->usuarios = User::all();
        $this->selectedYear = Carbon::now()->year;
        $this->tipos = LogsTipes::all();

    }


    public function render()
    {
        $this->actualizarLogs(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.logs-table', [
            'logs' => $this->logs
        ]);
    }

    protected function actualizarLogs()
    {
        $query = LogActions::when($this->buscar, function ($query) {
            $query->whereHas('usuario', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('log_actions.created_at', $this->selectedYear);
            })
            ->when($this->usuario, function ($query) {
                $query->where('log_actions.admin_user_id', $this->usuario);
            })
            ->when($this->tipo, function ($query) {
                $query->where('log_actions.tipo', $this->tipo);
            })
            ->orWhere('action', 'like', '%' . $this->buscar . '%')
            ->orWhere('description', 'like', '%' . $this->buscar . '%');
        })
        ->join('admin_user', 'log_actions.admin_user_id', '=', 'admin_user.id')
        ->select('log_actions.*','admin_user.name as usuario');

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->logs = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedUser' || $propertyName === 'selectedAnio' || $propertyName === 'selectedMes') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

}
