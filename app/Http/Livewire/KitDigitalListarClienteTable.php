<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\KitDigitalServicios;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class KitDigitalListarClienteTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $selectedGestor;
    public $selected;
    public $selectedServicio;
    public $selectedEstadoFactura;
    public $selectedComerciales;
    public $selectedSegmento;
    public $selectedDateField; // Para almacenar el campo de fecha seleccionado
    public $dateFrom;          // Fecha desde
    public $dateTo;            // Fecha hasta
    public $clientes;
    public $estados;
    public $gestores;
    public $servicios;
    public $comerciales;
    public $estados_facturas;
    public $segmentos;
    public $Sumatorio;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $kitDigitals; // Propiedad protegida para los usuarios

    public function mount(){

        $this->gestores = User::Where('access_level_id',4)->where('inactive', 0)->get();
        $this->comerciales = User::whereIn('access_level_id', [1, 6])->where('inactive', 0)->get();
        $this->servicios = KitDigitalServicios::all();
        $this->estados = KitDigitalEstados::orderBy('orden', 'asc')->get();

        $this->estados_facturas = [
            ['id' => '0', 'nombre' => 'No abonada'],
            ['id' => '1', 'nombre' => 'Abonada'],
        ];
        $this->segmentos  = [
            ['id' => '1', 'nombre' => '1'],
            ['id' => '2', 'nombre' => '2'],
            ['id' => '3', 'nombre' => '3'],
            ['id' => '30', 'nombre' => '3 Extra'],
            ['id' => '4', 'nombre' => '4'],
            ['id' => '5', 'nombre' => '5'],
            ['id' => 'A', 'nombre' => 'A'],
            ['id' => 'B', 'nombre' => 'B'],
            ['id' => 'C', 'nombre' => 'C']
        ];
    }


    public function render()
    {
        $this->clientes = Client::where('is_client',true)->get();
        $this->actualizarKitDigital(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.kit-digital-listar', [
            'kitDigitals' => $this->kitDigitals
        ]);
    }

    protected function actualizarKitDigital()
{
    // Comprueba si se ha seleccionado "Todos" para la paginación
    $buscarLower = mb_strtolower(trim($this->buscar), 'UTF-8');  // Convertir la cadena a minúsculas y eliminar espacios al inicio y al final
    $searchTerms = explode(" ", $buscarLower);  // Dividir la entrada en términos individuales

    $query = KitDigital::when($searchTerms, function ($query) use ($searchTerms) {
        foreach ($searchTerms as $term) {
            $query->where(function($q) use ($term) {
                $q->orWhereRaw('LOWER(contratos) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(cliente) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(expediente) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(contacto) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(importe) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(telefono) LIKE ?', ["%{$term}%"]);
            });
        }
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
    })
    ->when($this->dateFrom && $this->dateTo && $this->selectedDateField, function ($query) {
        $query->whereBetween($this->selectedDateField, [$this->dateFrom, $this->dateTo]);
    });

    // Calcula el sumatorio sobre todos los registros filtrados
    $this->Sumatorio = $query->get()->reduce(function ($carry, $item) {
        $cleanImporte = preg_replace('/[^\d,]/', '', $item->importe); // Elimina todo excepto números y coma
        $cleanImporte = str_replace(',', '.', $cleanImporte); // Convierte comas a puntos para decimales
        return $carry + (float)$cleanImporte;
    }, 0);

    // Aplica el orden
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente' || $propertyName === 'selectedEstado' || $propertyName === 'selectedGestor' || $propertyName === 'selectedServicio' || $propertyName === 'selectedEstadoFactura' || $propertyName === 'selectedComerciales') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
