<?php

namespace App\Exports;

use App\Models\Invoices\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExport implements FromCollection, WithHeadings
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    /**
     * Retorna los datos a exportar.
     */
    public function collection()
    {
        return $this->invoices->map(function($invoice) {
            return [
                $invoice->reference,
                optional($invoice->cliente)->name ?? 'Cliente borrado',
                optional($invoice->project)->name ?? 'Sin campaña asignada',
                $invoice->created_at->format('d/m/Y'),
                optional($invoice->invoiceStatus)->name ?? 'Sin estado asignado',
                $invoice->total,
                optional($invoice->adminUser)->name ?? 'Sin gestor asignado',
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
