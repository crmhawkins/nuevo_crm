<?php

namespace App\Http\Livewire;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\estadosDominios;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\KitDigitalServicios;
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
    public $selectedGestor;
    public $selectedSegmento;
    public $selectedServicio;
    public $selectedEstadoFactura;
    public $selectedComerciales;
    public $clientes;
    public $estados;
    public $gestores;
    public $servicios;
    public $comerciales;
    public $estados_facturas;
    public $segmentos;
    public $perPage = 10;
    public $sortColumn = 'cliente_id'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto

    protected $kitDigitals; // Propiedad protegida para los usuarios

    public function mount(){

        $this->gestores = User::Where('access_level_id',4)->get();
        $this->comerciales = User::Where('access_level_id',6)->get();
        $this->servicios = KitDigitalServicios::all();
        $this->estados = KitDigitalEstados::all();
        $this->clientes = Client::where('is_client',true)->get();
        $this->estados_facturas = [
            ['id' => '0', 'nombre' => 'No abonada'],
            ['id' => '1', 'nombre' => 'Abonada'],
        ];
        $this->segmentos = [
            ['id' => '1', 'nombre' => 'Segmento 1'],
            ['id' => '2', 'nombre' => 'Segmento 2'],
            ['id' => '3', 'nombre' => 'Segmento 3'],
            ['id' => '30', 'nombre' => 'Segmento 3 Extra'],
            ['id' => '4', 'nombre' => 'Segmento 4'],
            ['id' => '5', 'nombre' => 'Segmento 5'],
            ['id' => 'A', 'nombre' => 'Segmento A'],
            ['id' => 'B', 'nombre' => 'Segmento B'],
            ['id' => 'C', 'nombre' => 'Segmento C']
        ];
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
                    $query->where('contratos', 'like', '%' . $this->buscar . '%')
                        ->orWhere('cliente', 'like', '%' . $this->buscar . '%')
                        ->orWhere('expediente', 'like', '%' . $this->buscar . '%')
                        ->orWhere('contacto', 'like', '%' . $this->buscar . '%')
                        ->orWhere('telefono', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedComerciales, function ($query) {
                    $query->where('comercial_id', $this->selectedComerciales);
                })
                ->when($this->selectedEstadoFactura, function ($query) {
                    $query->where('estado_factura', $this->selectedEstadoFactura);
                })
                ->when($this->selectedServicio, function ($query) {
                    $query->where('servicio_id', $this->selectedServicio);
                })
                ->when($this->selectedSegmento, function ($query) {
                    $query->where('segmento', $this->selectedSegmento);
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('gestor', $this->selectedGestor);
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('cliente_id', $this->selectedCliente);
                })
                ->when($this->selectedEstado, function ($query) {
                    $query->where('estado', $this->selectedEstado);
                });


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
