<?php

namespace App\Http\Livewire;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\estadosDominios;
use App\Models\Users\User;
use App\Models\Users\UserAccessLevel;
use App\Models\Users\UserDepartament;
use Livewire\Component;
use Livewire\WithPagination;

class DominiosTable extends Component
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
    protected $dominios; // Propiedad protegida para los usuarios

    public function mount(){
        $this->clientes = Client::all();
        $this->estados = estadosDominios::all();
        // $this->actualizarServicios(); // Inicializa los usuarios
    }


    public function render()
    {
        $this->actualizarDominios(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.dominios-table', [
            'dominios' => $this->dominios
        ]);
    }

    protected function actualizarDominios()
    {
        $query = Dominio::when($this->buscar, function ($query) {
                    $query->where('dominio', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('client_id', $this->selectedCliente);
                })
                ->when($this->selectedEstado, function ($query) {
                    $query->where('estado_id', $this->selectedEstado);
                });

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->dominios = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getCategorias()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->dominios;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarDominios(); // Luego actualizas la lista de usuarios basada en los filtros
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
