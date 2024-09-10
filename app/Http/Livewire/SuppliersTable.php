<?php

namespace App\Http\Livewire;


use App\Models\Suppliers\Supplier;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class SuppliersTable extends Component
{
    use WithPagination;

    public $buscar;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $suppliers; // Propiedad protegida para los usuarios


    public function render()
    {

        $this->actualizarPresupuestos(); // Inicializa los presupuestos
        return view('livewire.suppliers-table', [
            'suppliers' => $this->suppliers // Utiliza un método para obtener los presupuestos
        ]);
    }

    protected function actualizarPresupuestos()
    {
        $query = Supplier::
            when($this->buscar, function ($query) {
                $query->where('name', 'like', '%' . $this->buscar . '%')
                            ->orWhere('cif', 'like', '%' . $this->buscar . '%')
                            ->orWhere('phone', 'like', '%' . $this->buscar . '%')
                            ->orWhere('email', 'like', '%' . $this->buscar . '%');
            });

            // Aplica la ordenación
        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->suppliers = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getBudgets()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->budgets;
    }

    // Supongamos que tienes un método para actualizar los filtros
    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

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
        if ($propertyName === 'buscar' || $propertyName === 'selectedGestor' || $propertyName === 'selectedEstados' || $propertyName === 'perPage') {
            if($propertyName !== 'perPage' || $this->perPage !== 'all') {
                $this->resetPage();
            }
            // $this->actualizarPresupuestos();
        }

    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
