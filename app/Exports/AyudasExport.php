<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AyudasExport implements FromCollection, WithHeadings
{
    protected $kitDigitals;

    public function __construct($kitDigitals)
    {
        $this->kitDigitals = $kitDigitals;
    }

    /**
     * Retorna los datos a exportar.
     */
    public function collection()
    {
        return $this->kitDigitals->map(function($kitDigital) {
            return [
                $kitDigital->empresa,
                $kitDigital->segmento,
                optional($kitDigital->Client)->company ?? 'Sin cliente asociado',
                $kitDigital->cliente,
                $kitDigital->contacto,
                $kitDigital->telefono,
                $kitDigital->expediente,
                $kitDigital->contratos,
                optional($kitDigital->servicios)->name ?? 'Sin servicio asociado',
                optional($kitDigital->estados)->nombre ?? 'Sin estado asignado',
                \Carbon\Carbon::parse($kitDigital->created_at)->format('d/m/Y'),
                \Carbon\Carbon::parse($kitDigital->fecha_actualizacion)->format('d/m/Y'),
                $kitDigital->importe,
                $kitDigital->estado_factura,
                \Carbon\Carbon::parse($kitDigital->banco)->format('d/m/Y'),
                \Carbon\Carbon::parse($kitDigital->fecha_acuerdo)->format('d/m/Y'),
                \Carbon\Carbon::parse($kitDigital->plazo_maximo_entrega)->format('d/m/Y'),
                optional($kitDigital->gestor)->name ?? 'Sin gestor asignado',
                optional($kitDigital->comercial)->name ?? 'Sin comercial asignado',
                $kitDigital->comentario,
                $kitDigital->nuevo_comentario,
            ];
        });
    }

    /**
     * Retorna los encabezados de las columnas.
     */
    public function headings(): array
    {
        return [
            'empresa',
            'segmento',
            'Cli.Asociado',
            'Cliente',
            'Contacto',
            'Telefono',
            'Expediente',
            'Contratos',
            'Servicio',
            'Estado',
            'Fecha Creacion',
            'Fecha Actualizacion',
            'Importe',
            'Estado Factura',
            'En Banco',
            'F.Acuerdo',
            'Plazo Maximo',
            'Gestor',
            'Comercial',
            'Comentario',
            'N.Comentario'
        ];
    }
}
