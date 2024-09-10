<?php

namespace App\Http\Livewire;

use App\Models\Petitions\Petition;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;


class PetitionsUserTable extends Component
{
    use WithPagination;
    public $gestores;
    public $buscar;
    public $selectedEstados = 0;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $petitions; // Propiedad protegida para los usuarios

    public function mount(){
        $this->gestores = User::where('access_level_id', 4)->get();
    }

    public function render()
    {
        $this->actualizarPresupuestos(); // Inicializa los presupuestos
        return view('livewire.petitions-user-table', [
            'petitions' => $this->getPetitioms() // Utiliza un método para obtener los presupuestos
        ]);
    }

    protected function actualizarPresupuestos()
    {
        $query = Petition::where('admin_user_id',Auth()->user()->id)->
            when($this->buscar, function ($query) {
                $query->whereHas('cliente', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->buscar . '%')
                            ->orWhere('email', 'like', '%' . $this->buscar . '%');
            })
            ->orWhereHas('proyecto', function ($subQuery) { // Busca en los conceptos de presupuesto
                $subQuery->where('name', 'like', '%' . $this->buscar . '%');
            });
            })
            ->when($this->selectedEstados == 0 ||$this->selectedEstados == 1 , function ($query) {
                $query->where('finished', $this->selectedEstados);

            });

            // Aplica la ordenación
        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->petitions = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getPetitioms()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->petitions;
    }

    // Supongamos que tienes un método para actualizar los filtros
    public function aplicarFiltro()
    {

        $this->actualizarPresupuestos(); // Luego actualizas la lista de usuarios basada en los filtros
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
        if ($propertyName === 'buscar' ||  $propertyName === 'selectedEstados' || $propertyName === 'perPage') {
            if($propertyName !== 'perPage' || $this->perPage !== 'all') {
                $this->resetPage();
            }
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
