<?php

namespace App\Http\Livewire;

use App\Models\Bajas\Baja;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class BajasTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedUser;
    public $selectedAnio;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    protected $bajas;

    public function mount()
    {
        $this->usuarios = User::all();
    }

    public function render()
    {
        $this->actualizarBajas();
        return view('livewire.bajas-table', [
            'bajas' => $this->bajas
        ]);
    }

    protected function actualizarBajas()
    {
        $query = Baja::when($this->buscar, function ($query) {
            $query->where('observacion', 'like', '%' . $this->buscar . '%');
        })
        ->when($this->selectedUser, function ($query) {
            $query->where('admin_user_id', $this->selectedUser);
        })
        ->when($this->selectedAnio, function ($query) {
            $query->whereYear('inicio', $this->selectedAnio);
        })
        ->when($this->selectedMes, function ($query) {
            $query->whereMonth('inicio', $this->selectedMes);
        });

        $query->orderBy($this->sortColumn, $this->sortDirection);
        $this->bajas = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if (in_array($propertyName, ['buscar', 'selectedUser', 'selectedAnio', 'selectedMes'])) {
            $this->resetPage();
        }
    }
}
