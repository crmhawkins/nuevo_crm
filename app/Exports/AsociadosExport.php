<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsociadosExport implements FromCollection, WithHeadings
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
                $gasto->purchase_order_id ?? 'No tiene orden de compra',
                optional(optional($gasto->OrdenCompra)->cliente)->name ?? 'Sin cliente Asociado',
                optional(optional($gasto->OrdenCompra)->Proveedor)->name ?? 'Sin Proveedor Asociado',
                $gasto->title,
                $gasto->quantity,
                $gasto->iva_amount,
                $gasto->total_with_iva,                \Carbon\Carbon::parse($gasto->received_date)->format('d/m/Y'),
                optional($gasto->bankAccount)->name ?? 'Sin banco asignado',
                $gasto->state,
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
            'Referencia',
            'Nº orden',
            'Cliente',
            'Proveedor',
            'Titulo',
            'Cantidad',
            'Iva',
            'Total',
            'Fecha recepción',
            'Banco',
            'Estado',
            'Categoria'

        ];
    }
}
