<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Gasto;
use App\Models\Clients\Client;
use Livewire\Component;
use Livewire\WithPagination;

class GastosTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $selectedYear;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'title'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto
    protected $gastos; // Propiedad protegida para los gastosbusqueda


    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.gastos-table', [
            'gastos' => $this->gastos
        ]);
    }

    protected function actualizargastos()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación

        $query = Gasto::when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedYear, function ($query) {
                    $query->whereYear('created_at', $this->selectedYear);
                }); // Obtiene todos los registros sin paginación

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->gastos = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getGastos()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los gastos
        return $this->gastos;
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
    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizargastos(); // Luego actualizas la lista de gastos basada en los filtros
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente' || $propertyName === 'selectedEstado') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
