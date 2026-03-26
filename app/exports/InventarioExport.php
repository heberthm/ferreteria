<?php

namespace App\Exports;

use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InventarioExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithEvents
{
    /**
     * Filas extra que se insertan ANTES de los datos:
     *   Fila 1 → Título principal
     *   Fila 2 → Fecha de impresión
     *   Fila 3 → Separador vacío
     *   Fila 4 → Cabeceras de columnas  (las pone WithHeadings)
     *   Fila 5+ → Datos
     */
    private const HEADER_ROWS = 3;

    // ─────────────────────────────────────────────
    // Query optimizada — eager loading evita N+1
    // ─────────────────────────────────────────────
    public function query()
    {
        return Inventario::query()
            ->with(['producto.categoria', 'usuario'])
            ->select([
                'id_inventario',
                'id_producto',
                'tipo_movimiento',
                'cantidad',
                'stock_anterior',
                'stock_nuevo',
                'precio_compra',
                'costo_promedio',
                'precio_venta',
                'proveedor',
                'numero_factura',
                'fecha_movimiento',
                'notas',
                'userId',
            ])
            ->orderBy('id_inventario', 'desc');
    }

    // ─────────────────────────────────────────────
    // Cabeceras de columnas
    // ─────────────────────────────────────────────
    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Producto',
            'Categoría',
            'Tipo Movimiento',
            'Cantidad',
            'Stock Anterior',
            'Stock Nuevo',
            'Precio Compra',
            'Costo Promedio',
            'Precio Venta',
            'Proveedor',
            'Nº Factura',
            'Registrado por',
            'Observaciones',
        ];
    }

    // ─────────────────────────────────────────────
    // Mapeo fila por fila
    // ─────────────────────────────────────────────
    public function map($row): array
    {
        return [
            $row->id_inventario,
            $row->fecha_movimiento
                ? $row->fecha_movimiento->format('d/m/Y')
                : 'N/A',
            $row->producto ? $row->producto->nombre : 'Producto eliminado',
            $row->producto && $row->producto->categoria
                ? $row->producto->categoria->nombre
                : 'N/A',
            $this->formatTipo($row->tipo_movimiento),
            $row->cantidad,
            $row->stock_anterior,
            $row->stock_nuevo,
            $row->precio_compra  ? number_format($row->precio_compra,  2, '.', '') : 'N/A',
            $row->costo_promedio ? number_format($row->costo_promedio, 2, '.', '') : 'N/A',
            $row->precio_venta   ? number_format($row->precio_venta,   2, '.', '') : 'N/A',
            $row->proveedor      ?? 'N/A',
            $row->numero_factura ?? 'N/A',
            $row->usuario        ? $row->usuario->name : 'N/A',
            $row->notas          ?? '',
        ];
    }

    // ─────────────────────────────────────────────
    // Nombre de la pestaña
    // ─────────────────────────────────────────────
    public function title(): string
    {
        return 'Kardex Inventario';
    }

    // ─────────────────────────────────────────────
    // WithStyles — delegado a AfterSheet
    // ─────────────────────────────────────────────
    public function styles(Worksheet $sheet)
    {
        return [];
    }

    // ─────────────────────────────────────────────
    // AfterSheet: título, fecha e estilos globales
    // ─────────────────────────────────────────────
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastCol = 'O'; // 15 columnas → columna O

                // 1. Insertar 3 filas al inicio
                $sheet->insertNewRowBefore(1, self::HEADER_ROWS);

                // ── Fila 1: Título ──────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'KARDEX DE INVENTARIO');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 16,
                        'color' => ['argb' => 'FFFFFFFF'],
                        'name'  => 'Arial',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1E3A5F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(38);

                // ── Fila 2: Fecha y hora de impresión ───────────────────
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue(
                    'A2',
                    now()->format('d/m/Y  H:i:s')
                );
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size'   => 11,
                        'color'  => ['argb' => 'FFB0C4DE'],
                        'name'   => 'Arial',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1E3A5F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // ── Fila 3: Separador vacío ──────────────────────
                $sheet->getRowDimension(3)->setRowHeight(6);

                // ── Fila 4: Cabeceras de columnas ────────────────
                $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 11,
                        'color' => ['argb' => 'FFFFFFFF'],
                        'name'  => 'Arial',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF2E5D8E'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFAAAAAA'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(22);

                // ── Filas de datos: zebra striping ───────────────
                $lastRow = $sheet->getHighestRow();
                for ($i = 5; $i <= $lastRow; $i++) {
                    $bg = ($i % 2 === 0) ? 'FFF2F6FC' : 'FFFFFFFF';
                    $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                        'font' => ['name' => 'Arial', 'size' => 10],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => $bg],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFE0E0E0'],
                            ],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                // ── Anchos de columna ────────────────────────────
                $widths = [
                    'A' => 6,  'B' => 12, 'C' => 28, 'D' => 16,
                    'E' => 14, 'F' => 10, 'G' => 14, 'H' => 12,
                    'I' => 14, 'J' => 14, 'K' => 13, 'L' => 20,
                    'M' => 14, 'N' => 18, 'O' => 30,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // ── Congelar paneles a partir de fila 5 ─────────
                $sheet->freezePane('A5');
            },
        ];
    }

    // ─────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────
    private function formatTipo(string $tipo): string
    {
        return match ($tipo) {
            'entrada'    => 'Entrada',
            'salida'     => 'Salida',
            'ajuste'     => 'Ajuste',
            'devolucion' => 'Devolución',
            default      => ucfirst($tipo),
        };
    }
}