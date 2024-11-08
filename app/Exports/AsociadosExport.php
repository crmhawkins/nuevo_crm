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
                number_format($gasto->quantity, 2,',','.'),
                \Carbon\Carbon::parse($gasto->received_date)->format('d/m/Y'),
                optional($gasto->bankAccount)->name ?? 'Sin banco asignado',
                $gasto->state,
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
            'Fecha recepción',
            'Banco',
            'Estado',
        ];
    }
}
