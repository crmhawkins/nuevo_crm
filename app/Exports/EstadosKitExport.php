<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EstadosKitExport implements FromCollection, WithHeadings
{
    protected $registros;
    protected $mostrarEmpresa;

    public function __construct(Collection $registros, bool $mostrarEmpresa = false)
    {
        $this->registros = $registros;
        $this->mostrarEmpresa = $mostrarEmpresa;
    }

    public function collection()
    {
        return $this->registros->map(function ($item) {
            return [
                'ID' => $item->reference_id,
                'Contrato' => $item->contratos,
                'Categoría de días' => $item->categoria_dias,
                'Estado' => $this->traducirEstado($item->estado),
                'Fecha de estado' => $item->fecha_estado,
                'SASAK enviado' => $item->fecha_sasak,
                'Empresa' => $this->mostrarEmpresa ? $item->empresa ?? '' : null,
            ];
        });
    }

    public function headings(): array
    {
        $headers = [
            'ID',
            'Contrato',
            'Categoría de días',
            'Estado',
            'Fecha de estado',
            'SASAK enviado',
        ];

        if ($this->mostrarEmpresa) {
            $headers[] = 'Empresa';
        }

        return $headers;
    }

    private function traducirEstado($codigo)
    {
        $estados = [
            8 => 'Justificado',
            9 => 'Justificado parcial',
            10 => 'Validada',
            12 => 'Pendiente subsanar 1',
            13 => 'Pendiente subsanar 2',
            14 => 'Subsanado 1',
            15 => 'Subsanado 2',
            20 => 'Pendiente 2ª Justificacion',
            21 => '2º Justificacion Realizada',
            25 => 'Validada 2ª justificacion',
            29 => 'Subsanado 3',
            30 => 'SASAK',
            31 => 'R SASAK',
            32 => '2º Subsanado 1',
            33 => '2º Subsanado 2',
            34 => '2º Subsanado 3',
            35 => 'Subsanacion incorrecta',
            36 => 'Finalizado plazo de subsanacion',
            37 => 'C.aleatoria',
            39 => 'AUDITORIA',
        ];

        return $estados[$codigo] ?? 'Desconocido';
    }
}
