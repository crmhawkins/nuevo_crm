<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IngresosExport implements FromCollection, WithHeadings
{
    protected $ingresos;

    public function __construct($ingresos)
    {
        $this->ingresos = $ingresos;
    }

    /**
     * Retorna los datos a exportar.
     */
    public function collection()
    {
        return $this->ingresos->map(function($ingreso) {
            return [
                $ingreso->id,
                optional($ingreso->bankAccount)->name ?? 'Sin banco asignado',
                $ingreso->title,
                $ingreso->quantity,
                \Carbon\Carbon::parse($ingreso->date)->format('d/m/Y'),
                optional($ingreso->getInvoice)->reference ?? 'Sin factura asociada',
                \Carbon\Carbon::parse($ingreso->created_at)->format('d/m/Y'),
            ];
        });
    }

    /**
     * Retorna los encabezados de las columnas.
     */
    public function headings(): array
    {
        return [
            'Id',
            'Banco',
            'Titulo',
            'Cantidad',
            'Fecha',
            'Factura Asociada',
            'Fecha Creación'
        ];
    }
}
