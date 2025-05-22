<?php

namespace App\Http\Livewire;

use App\Models\CrmActivities\CrmActivitiesMeetings;
use App\Models\Users\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ActasTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedUser;
    public $selectedYear;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto

    protected $contratos; // Propiedad protegida para los usuarios

    public function mount(){
        $this->usuarios = User::all();
        $this->selectedYear = Carbon::now()->year;

    }


    public function render()
    {
        $this->actualizarNominas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.actas-table', [
            'contratos' => $this->contratos
        ]);
    }

    protected function actualizarNominas()
    {
        $query = CrmActivitiesMeetings::when($this->buscar, function ($query) {
            $query->whereHas('client', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->buscar . '%');
            })
            ->orWhereHas('adminUser', function ($subQuery) { // Busca en los conceptos de presupuesto
                $subQuery->where('name', 'like', '%' . $this->buscar . '%')
                    ->orWhere('surname', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('crm_activities_meetings.created_at', $this->selectedYear);
            })
            ->orWhere('subject', 'like', '%' . $this->buscar . '%')
            ->orWhere('date', 'like', '%' . $this->buscar . '%');
        })
        ->join('clients', 'crm_activities_meetings.client_id', '=', 'clients.id')
        ->join('admin_user', 'crm_activities_meetings.admin_user_id', '=', 'admin_user.id')
        ->select('crm_activities_meetings.*', 'clients.name as cliente','admin_user.name as usuario');

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->contratos = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
