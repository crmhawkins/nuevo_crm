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
    public $fechaInicio = '';
    public $fechaFin = '';
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    public $filtroSinFacturas = false; // Nuevo filtro para dominios sin facturas
    public $añoSinFacturas = ''; // Año para filtrar dominios sin facturas
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
        $query = Dominio::with(['cliente', 'estadoName'])
                ->when($this->buscar, function ($query) {
                    $query->where('dominio', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('client_id', $this->selectedCliente);
                })
                ->when($this->selectedEstado, function ($query) {
                    $query->where('estado_id', $this->selectedEstado);
                })
                ->when($this->fechaInicio, function ($query) {
                    $query->where('date_end', '>=', $this->fechaInicio);
                })
                ->when($this->fechaFin, function ($query) {
                    $query->where('date_end', '<=', $this->fechaFin);
                })
                ->when($this->filtroSinFacturas && $this->añoSinFacturas, function ($query) {
                    // Filtrar dominios que NO tienen facturas asociadas en el año especificado
                    $query->whereDoesntHave('cliente.presupuestos.factura.invoiceConcepts', function ($subQuery) {
                        $subQuery->whereYear('created_at', $this->añoSinFacturas)
                                ->where(function ($q) {
                                    $q->where('title', 'like', '%dominio%')
                                      ->orWhere('concept', 'like', '%dominio%')
                                      ->orWhere('title', 'like', '%Dominio%')
                                      ->orWhere('concept', 'like', '%Dominio%')
                                      ->orWhere('title', 'like', '%DOMINIO%')
                                      ->orWhere('concept', 'like', '%DOMINIO%');
                                });
                    });
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente' || $propertyName === 'selectedEstado' || $propertyName === 'fechaInicio' || $propertyName === 'fechaFin' || $propertyName === 'filtroSinFacturas' || $propertyName === 'añoSinFacturas') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function limpiarFiltrosFecha()
    {
        $this->fechaInicio = '';
        $this->fechaFin = '';
        $this->resetPage();
    }

    public function updatedFechaInicio()
    {
        // Validar que fecha inicio no sea mayor que fecha fin
        if ($this->fechaInicio && $this->fechaFin && $this->fechaInicio > $this->fechaFin) {
            $this->fechaFin = $this->fechaInicio;
        }
        $this->resetPage();
        $this->actualizarDominios();
    }

    public function updatedFechaFin()
    {
        // Validar que fecha fin no sea menor que fecha inicio
        if ($this->fechaInicio && $this->fechaFin && $this->fechaInicio > $this->fechaFin) {
            $this->fechaInicio = $this->fechaFin;
        }
        $this->resetPage();
        $this->actualizarDominios();
    }

    public function filtroRango30Dias()
    {
        $this->fechaInicio = now()->format('Y-m-d');
        $this->fechaFin = now()->addDays(30)->format('Y-m-d');
        $this->resetPage();
        $this->actualizarDominios();
    }

    public function filtroRango90Dias()
    {
        $this->fechaInicio = now()->format('Y-m-d');
        $this->fechaFin = now()->addDays(90)->format('Y-m-d');
        $this->resetPage();
        $this->actualizarDominios();
    }

    public function filtroVencidos()
    {
        $this->fechaInicio = '';
        $this->fechaFin = now()->format('Y-m-d');
        $this->resetPage();
        $this->actualizarDominios();
    }

    public function filtroEsteMes()
    {
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
        $this->actualizarDominios();
    }

    public function activarFiltroSinFacturas()
    {
        $this->filtroSinFacturas = true;
        $this->añoSinFacturas = now()->year; // Año actual por defecto
        $this->resetPage();
    }

    public function desactivarFiltroSinFacturas()
    {
        $this->filtroSinFacturas = false;
        $this->añoSinFacturas = '';
        $this->resetPage();
    }

    public function updatedAñoSinFacturas()
    {
        if ($this->añoSinFacturas) {
            $this->filtroSinFacturas = true;
        }
        $this->resetPage();
    }

    public function testFiltros()
    {
        \Illuminate\Support\Facades\Log::info('Filtros actuales:', [
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
            'buscar' => $this->buscar,
            'selectedCliente' => $this->selectedCliente,
            'selectedEstado' => $this->selectedEstado,
            'filtroSinFacturas' => $this->filtroSinFacturas,
            'añoSinFacturas' => $this->añoSinFacturas
        ]);
    }
}
