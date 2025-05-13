<?php

namespace App\Http\Livewire;

use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\Logs\LogActions;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LogskitTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedYear;
    public $selectedEstado;
    public $tipos;
    public $usuarios;
    public $usuario;
    public $perPage = 10;
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $estados = [];
    public $invertir = false;
    public $fechasSasak = [];


    public $ordenEstado = null;
    public $ordenDireccion = 'asc';
    private $logs;
    private $logsPivotados;
    public $columnasEstados = [];
    public $columnasOcultas = [];
    public $mostrarSoloConValor = false;

    public $estadoSeleccionado;
    public $importeTotal = 0;
    public $columnasSeleccionadasTemp = [];
    public $seleccionInicialHecha = false;
    public $fechasEditables = []; // [reference_id][estado] => 'yyyy-mm-dd'
    protected $listeners = ['llamarActualizarFecha' => 'actualizarFecha'];

    public function mount()
{
    $this->usuarios = User::where('inactive', 0)->get();
    $this->logsPivotados = collect();
    $this->estados = KitDigitalEstados::orderBy('orden', 'asc')->get();

    $this->columnasOcultas = [];

    // Evita preseleccionar si ya hay columnas en la sesión (por navegación o actualización Livewire)
    if (empty($this->columnasSeleccionadasTemp)) {
        $this->columnasSeleccionadasTemp = []; // <-- Comienza vacío, no preseleccionamos nada
    }
}


    public function render()
    {
        // ⚠️ primero resuelve el invertir, antes de actualizar logs
        if ($this->invertir) {
            $this->columnasSeleccionadasTemp = array_values(array_diff(
                $this->columnasEstados,
                $this->columnasSeleccionadasTemp
            ));
            $this->invertir = false;
            $this->seleccionInicialHecha = true; // 🔥 esto evita que se reescriba en actualizarLogs()

        }

        // Luego ya puedes construir los logs
        $this->actualizarLogs();

        return view('livewire.logskit-table', [
            'logsPivotados' => $this->logsPivotados,
            'columnasEstados' => $this->columnasEstados,
            'estados' => $this->estados,
        ]);
    }


    protected function selectedEstados()
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
            ->when($this->selectedEstado, function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('ayudas', function ($subQuery) {
                        $subQuery->where('ayudas.estado', $this->selectedEstado);
                    });
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

    }

    protected function actualizarLogs()
    {
        $idsKits = null;

        if ($this->buscar) {
            $idsKits = \App\Models\KitDigital::where('contratos', 'like', '%' . $this->buscar . '%')
                ->pluck('id')
                ->toArray();
        }

        $query = LogActions::query()
            ->where('tipo', 1)
            ->where('action', 'Actualizar estado en kit digital')
            ->when($this->buscar, function ($query) use ($idsKits)  {
                $query->where(function ($query) use ($idsKits) {
                    $query->whereHas('usuario', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->buscar . '%');
                    })
                    ->orWhereHas('ayudas', function ($subQuery) {
                        $subQuery->where('ayudas.cliente', 'like', '%' . $this->buscar . '%')
                                 ->orWhere('ayudas.contratos', 'like', '%' . $this->buscar . '%');
                    })
                    ->orWhere('description', 'like', '%' . $this->buscar . '%')
                    ->orWhere('reference_id', 'like', '%' . $this->buscar . '%');
                    // 👇 Aquí añadimos los kits por coincidencia en contratos
                    if (!empty($idsKits)) {
                        $query->orWhereIn('reference_id', $idsKits);
                    }
                });

            })
            ->when($this->estadoSeleccionado, fn($query) =>
                $query->where('ayudas.estado', $this->estadoSeleccionado)
            )
            ->when($this->selectedYear, fn($query) => $query->whereYear('log_actions.created_at', $this->selectedYear))
            ->when($this->usuario, fn($query) => $query->where('log_actions.admin_user_id', $this->usuario))
            ->join('admin_user', 'log_actions.admin_user_id', '=', 'admin_user.id')
            ->join('ayudas', 'ayudas.id', '=', 'log_actions.reference_id')
            ->join('ayudas_servicios', 'ayudas_servicios.id', '=', 'ayudas.servicio_id')
            ->select(
                'log_actions.*',
                'admin_user.name as usuario',
                'ayudas.cliente as cliente',
                'ayudas_servicios.name as servicio',
                'ayudas.contratos as KD',
                'ayudas.importe as importe',
                'ayudas.id as kit_id',
                'ayudas.sasak as sasak',
            )
            ->orderBy($this->sortColumn, $this->sortDirection);

        $this->logs = $query->get();
        $collection = $this->logs;

        // Inicializar fechas sasak y sasak2
        $this->fechasSasak = [];
        foreach ($collection as $log) {
            $this->fechasSasak[$log->kit_id]['sasak'] = $log->sasak ? Carbon::parse($log->sasak)->format('Y-m-d') : null;
        }

        // Extraer columnas de estados únicas
        $this->columnasEstados = collect($collection)
            ->map(function ($log) {
                $partes = explode('  a  "', $log->description);
                return count($partes) === 2 ? trim($partes[1], '"') : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Aplicar visibilidad de columnas si se ha seleccionado un estado
        // if ($this->estadoSeleccionado) {
        //     $estadoSeleccionadoNombre = KitDigitalEstados::find($this->estadoSeleccionado)?->nombre;

        //     if ($estadoSeleccionadoNombre && in_array($estadoSeleccionadoNombre, $this->columnasEstados)) {
        //         $this->columnasOcultas = array_values(array_diff($this->columnasEstados, [$estadoSeleccionadoNombre]));

        //         if (empty($this->columnasSeleccionadasTemp)) {
        //             $this->columnasSeleccionadasTemp = [$estadoSeleccionadoNombre];
        //         }
        //     }
        // }

        if ($this->estadoSeleccionado) {
            $estadoSeleccionadoNombre = KitDigitalEstados::find($this->estadoSeleccionado)?->nombre;

            if ($estadoSeleccionadoNombre && in_array($estadoSeleccionadoNombre, $this->columnasEstados)) {
                // Solo actualizar columnas si el usuario aún no ha hecho una selección manual
                if (!$this->seleccionInicialHecha) {
                    $this->columnasOcultas = array_values(array_diff($this->columnasEstados, [$estadoSeleccionadoNombre]));
                    $this->columnasSeleccionadasTemp = [$estadoSeleccionadoNombre];
                }
            }
        } else {
            // Si no hay filtro, mostrar todas solo si no ha habido selección
            if (!$this->seleccionInicialHecha) {
                $this->columnasOcultas = [];
                $this->columnasSeleccionadasTemp = $this->columnasEstados;
            }
        }



        // Recolección de datos pivotados
        $this->fechasEditables = [];
        $logsPivotadosCollection = $collection->groupBy('reference_id')->map(function ($items, $ref) {
            $row = [
                'id' => $items->first()->kit_id,
                'ref_id' => $items->first()->id,
                'cliente' => $items->first()->cliente,
                'servicio' => $items->first()->servicio,
                'KD' => $items->first()->KD,
                'importe' => $this->normalizarImporte($items->first()->importe),
            ];

            foreach ($items as $log) {
                $partes = explode('  a  "', $log->description);
                if (count($partes) === 2) {
                    $estado = trim($partes[1], '"');
                    $fecha = Carbon::parse($log->created_at)->format('Y-m-d');

                    $row[$estado] = $fecha;
                    $this->fechasEditables[$log->id][$estado] = $fecha;
                }
            }

            return $row;
        })->values();

        // Filtrar por columnas visibles
        // $logsPivotadosCollection = $logsPivotadosCollection->filter(function ($row) {
        //     foreach ($this->columnasEstados as $estado) {
        //         if (!in_array($estado, $this->columnasOcultas) && empty($row[$estado])) {
        //             return false;
        //         }
        //     }
        //     return true;
        // });

        $logsPivotadosCollection = $logsPivotadosCollection->filter(function ($row) {
            if ($this->mostrarSoloConValor) {
                foreach ($this->columnasEstados as $estado) {
                    if (!in_array($estado, $this->columnasOcultas) && empty($row[$estado])) {
                        return false;
                    }
                }
            }
            return true;
        });


        // Ordenamiento por estado si está activo
        if ($this->ordenEstado && $this->ordenEstado !== 'importe' && in_array($this->ordenEstado, $this->columnasEstados)) {
            $logsPivotadosCollection = $logsPivotadosCollection->filter(function ($row) {
                return !empty($row[$this->ordenEstado]);
            });
        }

        if ($this->sortColumn === 'importe') {
            $logsPivotadosCollection = $logsPivotadosCollection->sortBy(function ($row) {
                return $row['importe'];
            }, SORT_REGULAR, $this->sortDirection === 'desc')->values();
        }

        if ($this->ordenEstado && in_array($this->ordenEstado, $this->columnasEstados)) {
            $logsPivotadosCollection = $logsPivotadosCollection->sortBy(function ($row) {
                return $row[$this->ordenEstado] ?? '9999-99-99 99:99:99';
            }, SORT_REGULAR, $this->ordenDireccion === 'desc')->values();
        }

        $this->importeTotal = $logsPivotadosCollection->sum('importe');

        // Paginación final
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

    private function normalizarImporte($importe)
{
    $importe = trim($importe);

    // Caso 1: contiene punto como separador de miles y coma decimal (ej. 1.234,56)
    if (preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $importe)) {
        $importe = str_replace('.', '', $importe); // quitamos los miles
        $importe = str_replace(',', '.', $importe); // convertimos decimal a punto
    }

    // Caso 2: contiene coma como decimal sin miles (ej. 1234,56)
    elseif (preg_match('/^\d+,\d{2}$/', $importe)) {
        $importe = str_replace(',', '.', $importe);
    }

    // Caso 3: ya está con punto decimal (ej. 1234.56)
    elseif (preg_match('/^\d+\.\d{2}$/', $importe)) {
        // sin cambios
    }

    // Caso 4: número entero
    elseif (preg_match('/^\d+$/', $importe)) {
        $importe .= '.00';
    }

    return (float) $importe;
}


    public function actualizarFecha($referenceId, $estado, $nuevaFecha)
{
    try {
        $fechaFormateada = Carbon::parse($nuevaFecha)->format('Y-m-d');

        $log = LogActions::where('id', $referenceId)
            ->where('tipo', 1)
            ->where('action', 'Actualizar estado en kit digital')
            ->get()
            ->filter(function ($item) use ($estado) {
                $partes = explode('  a  "', $item->description);
                return count($partes) === 2 && trim($partes[1], '"') === $estado;
            })->first();

        if ($log) {
            $log->created_at = $fechaFormateada;
            $log->save();

            // 💡 Forzamos actualización REAL desde la base de datos
            $log->refresh();

            $this->dispatchBrowserEvent('notificacion', [
                'tipo' => 'success',
                'mensaje' => 'Fecha actualizada correctamente.',
            ]);

            // Esperamos un pequeño retraso para que se procese todo correctamente antes del render
            $this->resetPage(); // resetea la paginación por si cambia algo
        } else {
            $nuevolog = LogActions::Create([
                'tipo' => 1,
                'action' => 'Actualizar estado en kit digital',
                'reference_id' => $referenceId,
                'description' => 'De  ""  a  "' . $estado . '"',
                'created_at' => $nuevaFecha,
                'updated_at' => $nuevaFecha,
                'admin_user_id' => Auth::user()->id,
            ]);

            if ($nuevolog)
            {
                $this->dispatchBrowserEvent('notificacion', [
                    'tipo' => 'success',
                    'mensaje' => 'Fecha actualizada correctamente.',
                ]);
            }
        }

        $this->actualizarLogs();

    } catch (\Exception $e) {
        $this->dispatchBrowserEvent('notificacion', [
            'tipo' => 'error',
            'mensaje' => 'Error al actualizar: ' . $e->getMessage(),
        ]);
    }
}




    public function aplicarColumnasSeleccionadas()
    {
        $this->seleccionInicialHecha = true;
        $this->columnasOcultas = array_values(array_diff($this->columnasEstados, $this->columnasSeleccionadasTemp));
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

        // Asegurarse de que 'logsPivotados' sea una colección antes de llamar a 'filter'
        if ($this->logsPivotados instanceof \Illuminate\Support\Collection) {
            $this->logsPivotados = $this->logsPivotados->filter(function ($row) use ($estado) {
                return !empty($row[$estado]);
            });
        }
    }

    public function updating($propertyName)
    {
        if (in_array($propertyName, ['buscar', 'usuario', 'selectedYear', 'tipo', 'estadoSeleccionado'])) {
            $this->resetPage();

            if ($propertyName === 'estadoSeleccionado' && empty($this->estadoSeleccionado)) {
                $this->ordenEstado = null;
                $this->ordenDireccion = 'asc';
                // No tocar columnas si el usuario las está seleccionando manualmente
            }

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
        $this->invertir = true;
    }

    public function updatedFechasSasak($value, $key)
    {
        [$logId, $campo] = explode('.', $key);

        $log = KitDigital::find($logId);
        if ($log && in_array($campo, ['sasak', 'sasak2'])) {
            $log->$campo = $value ?: null; // Asignar null si $value está vacío
            $log->save();

            $this->dispatchBrowserEvent('notificacion', [
                'tipo' => 'success',
                'mensaje' => "Fecha {$campo} actualizada.",
            ]);
        }
    }


}
