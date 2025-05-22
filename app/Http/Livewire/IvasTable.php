<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Iva;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class IvasTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $ivas; // Propiedad protegida para los ivasbusqueda

    public function render()
    {
        $this->actualizarivas(); // Ahora se llama directamente en render para refrescar los ivas.
        return view('livewire.ivas-table', [
            'ivas' => $this->ivas
        ]);
    }

    protected function actualizarivas()
    {
        $query = Iva::when($this->buscar, function ($query) {
                    $query->where('nombre', 'like', '%' . $this->buscar . '%')
                    ->orWhere('valor', 'like', '%' . $this->buscar . '%');
                });

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->ivas = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarivas(); // Luego actualizas la lista de ivas basada en los filtros
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
