<?php

namespace App\Http\Livewire;

use App\Models\Contratos\Contrato;
use App\Models\Nominas\Nomina;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class ContratosUserTable extends Component
{
    use WithPagination;

    public $identificador;
    public $buscar;
    public $selectedUser;
    public $selectedAnio;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'admin_user_id'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto

    protected $contratos; // Propiedad protegida para los usuarios

    public function mount(){
        $this->usuarios = User::all();
    }


    public function render()
    {
        $this->actualizarNominas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.contratos-user-table', [
            'contratos' => $this->contratos
        ]);
    }

    protected function actualizarNominas()
    {
        $query = Contrato::where('admin_user_id',$this->identificador)
        ->when($this->buscar, function ($query) {
            $query->where('name', 'like', '%' . $this->buscar . '%');
        })
        ->when($this->selectedAnio, function ($query) {
            $query->whereYear('fecha', $this->selectedAnio);
        })
        ->when($this->selectedMes, function ($query) {
            $query->whereMonth('fecha', $this->selectedMes);
        });

        $query->orderBy($this->sortColumn, $this->sortDirection);

        if ($this->perPage === 'all') {
            $this->contratos = $query->get();
        } else {
            $this->contratos = $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
        }
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedAnio' || $propertyName === 'selectedMes') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

}
