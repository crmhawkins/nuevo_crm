<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogKitExport implements FromCollection, WithHeadings
{
    protected $pivotados;
    protected $columnasVisibles;

    public function __construct(Collection $pivotados, array $columnasEstados, array $columnasOcultas = [])
    {
        $this->pivotados = $pivotados;
        // Calculamos solo las columnas que no estén ocultas
        $this->columnasVisibles = array_values(array_diff($columnasEstados, $columnasOcultas));
    }

    public function collection()
    {
        return $this->pivotados->map(function ($item) {
            $row = [
                'ID' => $item['id'],
                'Referencia' => $item['ref_id'],
                'Cliente' => $item['cliente'],
                'Servicio' => $item['servicio'],
                'KD' => $item['KD'],
                'Importe' => $item['importe'],
            ];

            foreach ($this->columnasVisibles as $estado) {
                $row[$estado] = $item[$estado] ?? '';
            }

            return $row;
        });
    }

    public function headings(): array
    {
        return array_merge(
            ['ID', 'Referencia', 'Cliente', 'Servicio', 'KD', 'Importe'],
            $this->columnasVisibles
        );
    }
}
