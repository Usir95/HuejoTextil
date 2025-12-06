<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalidaAlmacenExport implements FromArray, WithStyles, ShouldAutoSize {
    protected array $entradas;
    protected array $resumen;
    protected string $cliente;
    protected string $articulo;
    protected string $fechaCabecera;

    protected int $headerDetalleRow;
    protected int $resumenTitleRow;
    protected int $resumenHeaderRow;
    protected int $totalResumenRow;

    // filas footer
    protected int $precioRow;
    protected int $condicionesRow;
    protected int $observacionesRow;
    protected int $firmasRow;

    public function __construct(
        array $entradas,
        array $resumen,
        string $cliente = '',
        string $articulo = '',
        ?string $fecha = null
    ) {
        $this->entradas = $entradas;
        $this->resumen  = $resumen;
        $this->cliente  = $cliente;
        $this->articulo = $articulo;

        $this->fechaCabecera = $fecha
            ? Carbon::parse($fecha)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');
    }

    public function array(): array {
        $rows = [];

        // ===== ENCABEZADO =====
        $rows[] = ['SALIDA DE ALMACÉN DE PRODUCTO TERMINADO'];
        $rows[] = ['CLIENTE:', $this->cliente, '', '', 'FECHA:', $this->fechaCabecera];

        // ===== DETALLE =====
        $this->headerDetalleRow = count($rows) + 1;
        $rows[] = [
            'Tarjeta',
            'Rollo',
            'Producto',
            'Kgs',
            'Calidad',
            'Fecha y hora',
        ];

        foreach ($this->entradas as $item) {
            $fecha = '';

            if (!empty($item['fecha_movimiento'])) {
                try {
                    $fecha = Carbon::parse($item['fecha_movimiento'])->format('d/m/Y H:i');
                } catch (\Throwable $e) {
                    $fecha = $item['fecha_movimiento'];
                }
            }

            $rows[] = [
                $item['num_tarjeta'] ?? '',
                "'" . ($item['num_rollo'] ?? ''),
                $item['producto']['nombre'] ?? '',
                $item['cantidad'] ?? '',
                ($item['tipo_calidad_id'] ?? 0) == 1 ? 'Buena' : 'Regular',
                $fecha,
                // $item['cliente']['nombre'] ?? '',
            ];
        }

        // ===== RESUMEN =====
        $rows[] = [];                                                  // fila en blanco
        $this->resumenTitleRow = count($rows);                     // título resumen
        $rows[] = ['RESUMEN POR PRODUCTO'];

        $this->resumenHeaderRow = count($rows);                    // encabezado resumen
        $rows[] = ['Letra', 'Mts/Kgs', 'Rollos', 'Producto'];

        $letra       = 'A';
        $totalKg     = 0;
        $totalRollos = 0;

        foreach ($this->resumen as $res) {
            $kg     = (float)($res['total_kg'] ?? 0);
            $rollos = (int)($res['rollos'] ?? 0);

            $rows[] = [
                $letra,
                $kg,
                $rollos,
                $res['producto'] ?? '',
            ];

            $totalKg     += $kg;
            $totalRollos += $rollos;
            $letra++;
        }

        $this->totalResumenRow = count($rows);
        $rows[] = ['Total', $totalKg, $totalRollos];

        // ===== FOOTER (Precio / Plazo / Condiciones / Observaciones / Firmas) =====
        $rows[] = [];

        $this->precioRow = count($rows) + 1;
        $rows[] = ['Precio:', '', '', 'Plazo:', ''];

        $this->condicionesRow = count($rows) + 1;
        $rows[] = ['Condiciones:', ''];

        $this->observacionesRow = count($rows) + 1;
        $rows[] = ['Observaciones:', ''];

        $rows[] = [];

        $this->firmasRow = count($rows) + 1;
        $rows[] = [
            'ALMACENISTA',
            'VIGILANCIA',
            'CONDUCTOR',
            'CLIENTE',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet) {
        $blue  = 'FF0070C0';
        $green = 'FF00B050';

        // Título principal
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => $blue],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // CLIENTE / FECHA
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('C2')->getFont()->setBold(true);

        // Encabezado detalle
        $sheet->getStyle("A{$this->headerDetalleRow}:F{$this->headerDetalleRow}")
            ->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $blue],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

        // Título "RESUMEN POR PRODUCTO"
        $sheet->mergeCells("A{$this->resumenTitleRow}:D{$this->resumenTitleRow}");
        $sheet->getStyle("A{$this->resumenTitleRow}:D{$this->resumenTitleRow}")
            ->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $blue],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

        // Encabezado columnas resumen
        $sheet->getStyle("A{$this->resumenHeaderRow}:D{$this->resumenHeaderRow}")
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

        // Fila TOTAL
        $sheet->getStyle("A{$this->totalResumenRow}:C{$this->totalResumenRow}")
            ->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $green],
                ],
            ]);

        // Footer: etiquetas en negritas
        $sheet->getStyle("A{$this->precioRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->condicionesRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->observacionesRow}")->getFont()->setBold(true);

        // Fila de firmas completa en negritas y centrada
        $sheet->getStyle("A{$this->firmasRow}:F{$this->firmasRow}")
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

        return [];
    }
}
