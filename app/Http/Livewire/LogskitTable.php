<?php

namespace App\Http\Livewire;

use App\Models\Logs\LogActions;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public $ordenEstado = null;
    public $ordenDireccion = 'asc';
    private $logs;
    private $logsPivotados;
    public $columnasEstados = [];
    public $columnasOcultas = [];

    public function mount()
    {
        $this->usuarios = User::where('inactive', 0)->get();
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
        $query = LogActions::query()
            ->where('tipo', 1)
            ->where('action', 'Actualizar estado en kit digital')
            ->when($this->buscar, function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('usuario', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->buscar . '%');
                    })
                    ->orWhereHas('ayudas', function ($subQuery) {
                        $subQuery->where('ayudas.cliente', 'like', '%' . $this->buscar . '%')
                                ->orWhere('ayudas.contratos', 'like', '%' . $this->buscar . '%');
                    })
                    ->orWhere('description', 'like', '%' . $this->buscar . '%')
                    ->orWhere('reference_id', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->selectedYear, fn($query) => $query->whereYear('log_actions.created_at', $this->selectedYear))
            ->when($this->usuario, fn($query) => $query->where('log_actions.admin_user_id', $this->usuario))
            ->join('admin_user', 'log_actions.admin_user_id', '=', 'admin_user.id')
            ->join('ayudas', 'ayudas.id', '=', 'log_actions.reference_id')
            ->join('ayudas_servicios', 'ayudas_servicios.id', '=', 'ayudas.servicio_id')
            ->select('log_actions.*',
                    'admin_user.name as usuario',
                    'ayudas.cliente as cliente',
                    'ayudas_servicios.name as servicio',
                    'ayudas.contratos as KD' ,
                    'ayudas.importe as importe')
            ->orderBy($this->sortColumn, $this->sortDirection);

        $this->logs = $query->get();

        $collection = $this->logs;

        // Detectar todos los estados únicos
        $this->columnasEstados = collect($collection)
        ->map(function ($log) {
            $partes = explode('  a  "', $log->description);
            if (count($partes) === 2) {
                $estadoFinal = trim($partes[1], '"');
                return $estadoFinal;
            }
            return null;
        })
        ->filter()
        ->unique()
        ->sort()
        ->values()
        ->toArray();

        // Agrupar y pivotar los datos por 'reference_id'
        $logsPivotadosCollection = $collection->groupBy('reference_id')->map(function ($items, $ref) {
            $row = [
                'cliente' => $items->first()->cliente,
                'servicio' => $items->first()->servicio,
                'KD' => $items->first()->KD,
                'importe' => $items->first()->importe,
            ];

            // Guardar las fechas más recientes de cada estado
            $fechasPorEstado = [];

            foreach ($items as $log) {
                $partes = explode('  a  "', $log->description);
                if (count($partes) === 2) {
                    $estado = trim($partes[1], '"');
                    $fecha = Carbon::parse($log->created_at);

                    // Si ya existe una fecha para el estado, se mantiene la más reciente
                    if (!isset($fechasPorEstado[$estado]) || $fecha > $fechasPorEstado[$estado]) {
                        $fechasPorEstado[$estado] = $fecha;
                    }

                    $row[$estado] = $fecha->format('Y-m-d');
                }
            }

            return ['row' => $row, 'fechasPorEstado' => $fechasPorEstado];
        })->values();

        // Filtrar las filas basadas en la fecha más reciente
        if (count($this->columnasOcultas) == count($this->columnasEstados) - 1) {
            // Si solo hay una columna visible, filtrar por la fecha más reciente en ese estado
            $estadoVisible = array_diff($this->columnasEstados, $this->columnasOcultas)[0]; // La única columna visible
            $logsPivotadosCollection = $logsPivotadosCollection->filter(function ($data) use ($estadoVisible) {
                $row = $data['row'];
                $fechasPorEstado = $data['fechasPorEstado'];

                // Compara si la fecha más reciente de todos los estados coincide con la fecha del estado visible
                return isset($row[$estadoVisible]) && $row[$estadoVisible] === $fechasPorEstado[$estadoVisible]->format('Y-m-d');
            });
        }

        // Filtrar filas sin datos en columnas visibles
        $logsPivotadosCollection = $logsPivotadosCollection->filter(function ($data) {
            $row = $data['row'];
            foreach ($this->columnasEstados as $estado) {
                if (!in_array($estado, $this->columnasOcultas) && !empty($row[$estado])) {
                    return true;
                }
            }
            return false;
        });

        // Ordenar por estado (si aplica)
        if ($this->ordenEstado && in_array($this->ordenEstado, $this->columnasEstados)) {
            $logsPivotadosCollection = $logsPivotadosCollection->sortBy(function ($row) {
                return $row[$this->ordenEstado] ?? '9999-99-99 99:99:99';
            }, SORT_REGULAR, $this->ordenDireccion === 'desc')->values();
        }

        // Paginamos la colección final
        if ($this->perPage === 'all') {
            $this->logsPivotados = $logsPivotadosCollection;
        } else {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = intval($this->perPage);
            $currentItems = $logsPivotadosCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $this->logsPivotados = new LengthAwarePaginator(
                $currentItems,
                $logsPivotadosCollection->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
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

    public function ordenarPorEstado($estado)
    {
        if ($this->ordenEstado === $estado) {
            $this->ordenDireccion = $this->ordenDireccion === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenEstado = $estado;
            $this->ordenDireccion = 'asc';
        }
    }

    public function updating($propertyName)
    {
        if (in_array($propertyName, ['buscar', 'usuario', 'selectedYear', 'tipo'])) {
            $this->resetPage();
        }
    }

    public function toggleColumna($columna)
    {
        if (in_array($columna, $this->columnasOcultas)) {
            $this->columnasOcultas = array_values(array_diff($this->columnasOcultas, [$columna]));
        } else {
            $this->columnasOcultas[] = $columna;
        }
    }

    public function invertirColumnas()
    {
        $this->columnasOcultas = array_values(array_diff($this->columnasEstados, $this->columnasOcultas));
    }
}
