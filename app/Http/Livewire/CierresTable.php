<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Iva;
use App\Models\Accounting\LastYearsBalance;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CierresTable extends Component
{
    use WithPagination;

    public $buscar;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $cierres; // Propiedad protegida para los cierresbusqueda

    public function render()
    {
        $this->actualizarcierres(); // Ahora se llama directamente en render para refrescar los cierres.
        return view('livewire.cierres-table', [
            'cierres' => $this->cierres
        ]);
    }

    protected function actualizarcierres()
    {
        $query = LastYearsBalance::when($this->buscar, function ($query) {
                    $query->where('year', 'like', '%' . $this->buscar . '%');
                });

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->cierres = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarcierres(); // Luego actualizas la lista de cierres basada en los filtros
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente' || $propertyName === 'selectedEstado') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
