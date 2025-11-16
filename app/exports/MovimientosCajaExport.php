<?php

namespace App\Exports;

use App\Models\CajaMenor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CajaMenorExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return CajaMenor::with(['usuarioApertura', 'usuarioCierre'])
            ->orderBy('fecha_apertura', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Monto Inicial',
            'Monto Actual',
            'Estado',
            'Fecha Apertura',
            'Fecha Cierre',
            'Usuario Apertura',
            'Usuario Cierre',
            'Observaciones Apertura',
            'Observaciones Cierre'
        ];
    }

    public function map($caja): array
    {
        return [
            $caja->id,
            number_format($caja->monto_inicial, 2),
            number_format($caja->monto_actual, 2),
            ucfirst($caja->estado),
            $caja->fecha_apertura ? $caja->fecha_apertura->format('d/m/Y H:i') : 'N/A',
            $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'N/A',
            $caja->usuarioApertura ? $caja->usuarioApertura->name : 'N/A',
            $caja->usuarioCierre ? $caja->usuarioCierre->name : 'N/A',
            $caja->observaciones_apertura ?? 'Sin observaciones',
            $caja->observaciones_cierre ?? 'Sin observaciones'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para el encabezado
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E86C1']]
            ],
        ];
    }
}