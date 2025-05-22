<?php

namespace App\Http\Livewire\Plataforma;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Clients\Client;
use App\Models\Plataforma\WhatsappContacts;
use App\Models\Plataforma\CampaniasWhatsapp;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->search = request()->get('search', '');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $clients = Client::query()
            ->with(['whatsappContacts' => function($query) {
                $query->with('campania');
            }])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.plataforma.dashboard', [
            'clients' => $clients
        ]);
    }
}
