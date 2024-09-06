<?php

namespace App\Http\Livewire;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\estadosDominios;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\Users\User;
use App\Models\Users\UserAccessLevel;
use App\Models\Users\UserDepartament;
use Livewire\Component;
use Livewire\WithPagination;

class KitDigitalListarClienteTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'cliente_id'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto

    protected $kitDigitals; // Propiedad protegida para los usuarios

    public function mount(){
        $this->estados = KitDigitalEstados::all();
        $this->clientes = Client::where('is_client',true)->get();
    }


    public function render()
    {
        $this->actualizarKitDigital(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.kit-digital-listar', [
            'kitDigitals' => $this->kitDigitals
        ]);
    }

    protected function actualizarKitDigital()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación

        $query = KitDigital::when($this->buscar, function ($query) {
                    $query->where('contratos', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('cliente_id', $this->selectedCliente);
                })
                ->when($this->selectedEstado, function ($query) {
                    $query->where('estado', $this->selectedEstado);
                }); // Obtiene todos los registros sin paginación


        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->kitDigitals = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getCategorias()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->kitDigitals;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarKitDigital(); // Luego actualizas la lista de usuarios basada en los filtros
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
