<?php

namespace App\Exports;

use App\Models\Agremiado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // Para colores
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // Para empezar más abajo
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Auto-ancho de columnas
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgremiadosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    // 1. Creamos un contador privado
    private $rowNumber = 0;

    public function collection()
    {
        return Agremiado::all();
    }
    // Indicamos que la tabla de datos empiece en la fila 5
    public function startCell(): string
    {
        return 'A5';
    }

    // Definimos las columnas que queremos en el Excel
    public function headings(): array
    {
        return [
            'N°',
            'NRO DE COLEGIATURA',
            'FECHA DE INCORPORACIÓN DE COLEGIATURA',
            'N° DNI',
            'APELLIDOS',
            'NOMBRES',
            'SEXO',
            'HABIL, INHABIL, VITALICIO, FALLECIDO',
            'REGIÓN',
            'PROVINCIA',
            'DISTRITO',
            'GRADO ACADÉMICO',
            'ESPECIALIDAD',
            'SITUACIÓN LABORAL',
            'INSTITUCIÓN DONDE LABORA',
            'CARGO',
        ];
    }

    // Mapeamos los datos del modelo a las columnas
    public function map($agremiado): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber, // Columna N° automática
            $agremiado->matricula ?? '',
            $agremiado->fecha_matricula ?? '',
            $agremiado->dni ?? '',
            $agremiado->apellidos ?? '',
            $agremiado->nombres ?? '',
            $agremiado->sexo ?? '',
            $agremiado->es_vitalicio ? 'Vitalicio' : ($agremiado->estado ?? ''),
            $agremiado->sede ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Escribimos el título manualmente en las primeras filas
        $sheet->setCellValue('A1', 'COLEGIO DE CONTADORES PÚBLICOS DE HUÁNUCO');
        $sheet->mergeCells('A1:P1'); // Combinamos celdas

        $sheet->setCellValue('A2', 'REPORTE GENERAL DE AGREMIADOS - ' . date('d/m/Y'));
        $sheet->mergeCells('A2:P2');

        return [
            // Estilo para el Título Principal (Fila 1)
            1    => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F4E78'] // Azul oscuro institucional
                ],
            ],
            // Estilo para los encabezados de la tabla (Fila 5)
            5    => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '28A745'] // Verde (como tus widgets)
                ],
            ],
            // Bordes para toda la data (opcional)
            'A5:P2200' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
