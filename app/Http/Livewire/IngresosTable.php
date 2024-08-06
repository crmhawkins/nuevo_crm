<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Ingreso;
use App\Models\Clients\Client;
use Livewire\Component;
use Livewire\WithPagination;

class IngresosTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $clientes;
    public $estados;
    public $perPage = 10;

    protected $ingresos; // Propiedad protegida para los gastosbusqueda


    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.ingresos-table', [
            'ingresos' => $this->ingresos
        ]);
    }

    protected function actualizargastos()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        if ($this->perPage === 'all') {
            $this->ingresos = Ingreso::when($this->buscar, function ($query) {
                    $query->where('company_name', 'like', '%' . $this->buscar . '%');
                })
                ->get(); // Obtiene todos los registros sin paginación
        } else {
            // Usa paginación con la cantidad especificada por $this->perPage
            $this->ingresos = Ingreso::when($this->buscar, function ($query) {
                    $query->where('company_name', 'like', '%' . $this->buscar . '%');
                })
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10); // Se asegura de que $this->perPage sea numérico
        }
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
