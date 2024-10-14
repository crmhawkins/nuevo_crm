<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class gastosExport implements FromCollection, WithHeadings
{
    protected $gastos;

    public function __construct($gastos)
    {
        $this->gastos = $gastos;
    }

    /**
     * Retorna los datos a exportar.
     */
    public function collection()
    {
        return $this->gastos->map(function($gasto) {
            return [
                $gasto->reference,
                optional($gasto->cliente)->name ?? 'Cliente borrado',
                optional($gasto->project)->name ?? 'Sin campaña asignada',
                $gasto->created_at->format('d/m/Y'),
                optional($gasto->gastostatus)->name ?? 'Sin estado asignado',
                $gasto->total,
                optional($gasto->adminUser)->name ?? 'Sin gestor asignado',
            ];
        });
    }

    /**
     * Retorna los encabezados de las columnas.
     */
    public function headings(): array
    {
        return [
            'Referencia',
            'Cliente',
            'Campaña',
            'Fecha Creación',
            'Estado',
            'Total',
            'Gestor',
        ];
    }
}
