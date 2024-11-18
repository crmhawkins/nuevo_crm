<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GastosExport implements FromCollection, WithHeadings
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
                $gasto->id,
                optional($gasto->bankAccount)->name ?? 'Sin banco asignado',
                $gasto->title,
                $gasto->quantity,
                $gasto->iva_amount,
                $gasto->total_with_iva,
                \Carbon\Carbon::parse($gasto->received_date)->format('d/m/Y'),
                \Carbon\Carbon::parse($gasto->date)->format('d/m/Y'),
                $gasto->reference,
                $gasto->state,
                $gasto->transfer_movement ? 'Sí' : '',
                optional($gasto->categoria)->nombre ?? 'Sin categoria asignada',
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
            'Iva',
            'Total',
            'Fecha recepción',
            'Fecha de pago',
            'Referencia',
            'Estado',
            'Transferencia',
            'Categoria'
        ];
    }
}
