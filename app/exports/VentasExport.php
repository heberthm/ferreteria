<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VentasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Venta::with(['cliente', 'usuario', 'detalles'])
            ->select('ventas.*');

        // Aplicar filtros
        if (!empty($this->filters['fecha_desde'])) {
            $query->whereDate('ventas.fecha_venta', '>=', $this->filters['fecha_desde']);
        }

        if (!empty($this->filters['fecha_hasta'])) {
            $query->whereDate('ventas.fecha_venta', '<=', $this->filters['fecha_hasta']);
        }

        if (!empty($this->filters['estado'])) {
            $query->where('ventas.estado', $this->filters['estado']);
        }

        if (!empty($this->filters['metodo_pago'])) {
            $query->where('ventas.metodo_pago', $this->filters['metodo_pago']);
        }

        if (!empty($this->filters['cliente'])) {
            $query->whereHas('cliente', function($q) {
                $q->where('nombre', 'like', '%' . $this->filters['cliente'] . '%')
                  ->orWhere('cedula', 'like', '%' . $this->filters['cliente'] . '%');
            });
        }

        if (!empty($this->filters['factura'])) {
            $query->where('ventas.numero_factura', 'like', '%' . $this->filters['factura'] . '%');
        }

        return $query->orderBy('ventas.fecha_venta', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            ['HISTORIAL DE VENTAS'],
            ['Generado: ' . Carbon::now()->format('d/m/Y H:i:s')],
            [],
            [
                'FACTURA',
                'FECHA',
                'HORA',
                'CLIENTE',
                'DOCUMENTO',
                'VENDEDOR',
                'CANT. PRODUCTOS',
                'TOTAL',
                'MÉTODO PAGO',
                'ESTADO'
            ]
        ];
    }

    public function map($venta): array
    {
        $totalProductos = $venta->detalles->sum('cantidad');

        return [
            $venta->numero_factura ?? 'N/A',
            Carbon::parse($venta->fecha_venta)->format('d/m/Y'),
            Carbon::parse($venta->fecha_venta)->format('H:i'),
            $venta->cliente->nombre ?? 'Cliente General',
            $venta->cliente->cedula ?? 'N/A',
            $venta->usuario->name ?? 'N/A',
            $totalProductos,
            '$ ' . number_format($venta->total, 0, ',', '.'),
            ucfirst($venta->metodo_pago ?? 'N/A'),
            ucfirst($venta->estado ?? 'N/A')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true]]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 12,
            'C' => 10,
            'D' => 30,
            'E' => 15,
            'F' => 20,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 12,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
                
                $sheet->setAutoFilter('A4:J4');
                
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A4:J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                
                $sheet->getStyle('A4:J4')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4472C4');
                
                $sheet->getStyle('A4:J4')->getFont()->getColor()->setARGB('FFFFFFFF');
            },
        ];
    }
}