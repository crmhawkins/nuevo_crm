<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Gasto;
use App\Models\Clients\Client;
use App\Models\Holidays\Holidays;
use App\Models\Holidays\HolidaysPetitions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyholidaysTable extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $numberOfholidaysPetitions;
    public $holydayEvents;

    protected $holidays; // Propiedad protegida para los gastosbusqueda


    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.myholidays-table', [
            'holidays' => $this->holidays
        ]);
    }

    protected function actualizargastos()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        if ($this->perPage === 'all') {
            $this->holidays = HolidaysPetitions::where('admin_user_id', Auth::user()->id )->orderBy('created_at', 'asc')->get(); // Obtiene todos los registros sin paginación
        } else {
            // Usa paginación con la cantidad especificada por $this->perPage
            $this->holidays =  HolidaysPetitions::where('admin_user_id', Auth::user()->id )->orderBy('created_at', 'asc')
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10); // Se asegura de que $this->perPage sea numérico
        }
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
