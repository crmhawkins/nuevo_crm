<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\KitDigitalServicios;
use App\Models\Users\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class KitDigitalWhatsappPorContestacionTable extends Component
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
    public $sortColumn = 'ayudas.created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $kitDigitals; // Propiedad protegida para los usuarios

    public function mount(){

    }


    public function render()
    {
        $this->actualizarKitDigital(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.kit-digital-whatsapp-por-contestacion', [
            'kitDigitals' => $this->kitDigitals
        ]);
    }

    protected function actualizarKitDigital()
    {
        $buscarLower = mb_strtolower(trim($this->buscar), 'UTF-8');  // Convertir la cadena a minúsculas y eliminar espacios al inicio y al final
        $searchTerms = explode(" ", $buscarLower);  // Dividir la entrada en términos individuales

        $query = KitDigital::query()->join('envio_dani', 'envio_dani.kit_id', '=', 'ayudas.id');
        // Aplica el orden
        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->kitDigitals =  $query->get();
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
