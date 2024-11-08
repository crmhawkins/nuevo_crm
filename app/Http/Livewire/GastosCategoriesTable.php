<?php

namespace App\Http\Livewire;

use App\Models\Accounting\CategoriaGastos;
use Livewire\Component;
use Livewire\WithPagination;

class GastosCategoriesTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCategoria = '';
    public $perPage = 10;

    public $sortColumn = 'nombre'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $categorias; // Propiedad protegida para los usuarios
    public function render()
    {
        $this->actualizarServiciosCategoria(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.gastos-categories-table', [
            'categorias' => $this->categorias
        ]);
    }

    protected function actualizarServiciosCategoria()
    {

        $query = CategoriaGastos::when($this->buscar, function ($query) {
                    $query->where('name', 'like', '%' . $this->buscar . '%');
                });

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->categorias = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getCategorias()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->categorias;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarServiciosCategoria(); // Luego actualizas la lista de usuarios basada en los filtros
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCategoria') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
