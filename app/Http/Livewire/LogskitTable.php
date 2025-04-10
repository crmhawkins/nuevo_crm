<?php

namespace App\Http\Livewire;

use App\Models\Logs\LogActions;
use App\Models\Users\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LogskitTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedYear;
    public $tipo;
    public $tipos;
    public $usuarios;
    public $usuario;
    public $perPage = 10;
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    public $logs;
    public $logsPivotados;
    public $columnasEstados = [];

    public function mount()
    {
        $this->usuarios = User::where('inactive', 0)->get();
        $this->selectedYear = Carbon::now()->year;

    }

    public function render()
    {
        $this->actualizarLogs();

        return view('livewire.logskit-table', [
            'logsPivotados' => $this->logsPivotados,
            'columnasEstados' => $this->columnasEstados,
        ]);
    }

    protected function actualizarLogs()
    {
        $query = LogActions::when($this->buscar, function ($query) {
            $query->where('tipo', 1)
                ->where('action', 'Actualizar estado en kit digital')
                ->where(function ($query) {
                    $query->whereHas('usuario', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->buscar . '%');
                    })
                    ->whereHas('ayudas', function ($subQuery) {
                        $subQuery->where('ayudas.cliente', 'like', '%' . $this->buscar . '%');
                    })
                    ->orWhere('description', 'like', '%' . $this->buscar . '%')
                    ->orWhere('reference_id', 'like', '%' . $this->buscar . '%');
                });
        })
        ->when($this->selectedYear, fn($query) => $query->whereYear('log_actions.created_at', $this->selectedYear))
        ->when($this->usuario, fn($query) => $query->where('log_actions.admin_user_id', $this->usuario))
        ->join('admin_user', 'log_actions.admin_user_id', '=', 'admin_user.id')
        ->join('ayudas', 'ayudas.id', '=', 'log_actions.reference_id')
        ->select('log_actions.*', 'admin_user.name as usuario', 'ayudas.cliente as cliente', 'ayudas.servicio as servicio')
        ->orderBy($this->sortColumn, $this->sortDirection);

        $this->logs = $this->perPage === 'all'
            ? $query->get()
            : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);

        $collection = $this->perPage === 'all'
            ? $this->logs
            : $this->logs->getCollection();

        // Detectar todos los estados únicos
        $this->columnasEstados = collect($collection)
            ->map(function ($log) {
                if (preg_match('/a "(.*?)"/', $log->description, $matches)) {
                    return $matches[1];
                }
                return null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Agrupar y pivotar
        $this->logsPivotados = $collection->groupBy('reference_id')->map(function ($items, $ref) {
            $row = [
                'cliente' => $items->first()->cliente,
                'servicio' => $items->first()->servicio,

            ];

            foreach ($items as $log) {
                if (preg_match('/a "(.*?)"/', $log->description, $matches)) {
                    $estado = $matches[1];
                    $row[$estado] = Carbon::parse($log->created_at)->format('Y-m-d H:i:s');
                }
            }

            return $row;
        })->values();
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
        if (in_array($propertyName, ['buscar', 'usuario', 'selectedYear', 'tipo'])) {
            $this->resetPage();
        }
    }
}
