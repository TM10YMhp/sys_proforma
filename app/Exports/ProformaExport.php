<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProformaExport implements
  Export,
  WithEvents,
  WithStyles,
  WithColumnWidths
{
  private $products = [
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 30 cm x 22 cm',
      "cantidad" => 27,
      "medida" => 'UN',
      "precio_unitario" => 10,
      "total" => 270,
    ],
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 60 cm x 30 cm',
      "cantidad" => 20,
      "medida" => 'UN',
      "precio_unitario" => 24,
      "total" => 480,
    ],
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 30 cm x 30 cm',
      "cantidad" => 8,
      "medida" => 'UN',
      "precio_unitario" => 12.5,
      "total" => 100,
    ],
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 30 cm x 25 cm',
      "cantidad" => 2,
      "medida" => 'UN',
      "precio_unitario" => 10,
      "total" => 20,
    ],
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 100 cm x 60 cm',
      "cantidad" => 6,
      "medida" => 'UN',
      "precio_unitario" => 65,
      "total" => 390,
    ],
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 40 cm x 30 cm',
      "cantidad" => 8,
      "medida" => 'UN',
      "precio_unitario" => 15,
      "total" => 120,
    ],
    [
      "descripcion" => 'Cartel vinil adhesivo 15 cm x 21 cm',
      "cantidad" => 20,
      "medida" => 'UN',
      "precio_unitario" => 2.5,
      "total" => 50,
    ],
    [
      "descripcion" => 'Cartel vinil adhesivo 25 cm x 10 cm',
      "cantidad" => 22,
      "medida" => 'UN',
      "precio_unitario" => 3.5,
      "total" => 77,
    ],
    [
      "descripcion" => 'Cartel vinil adhesivo 20 cm x 20 cm',
      "cantidad" => 20,
      "medida" => 'UN',
      "precio_unitario" => 4,
      "total" => 180,
    ],
    [
      "descripcion" => 'Cartel vinil adhesivo A4',
      "cantidad" => 12,
      "medida" => 'UN',
      "precio_unitario" => 5,
      "total" => 60,
    ],
    [
      "descripcion" => 'Cartel vinil en base celtex 3 mm de 30 cm x 20 cm',
      "cantidad" => 20,
      "medida" => 'UN',
      "precio_unitario" => 9,
      "total" => 180,
    ]
  ];

  private function setupPage(Worksheet $sheet)
  {
    $sheet->getPageMargins()
      ->setTop(1.1)
      ->setBottom(0.9)
      ->setLeft(0.3)
      ->setRight(0.3)
      ->setHeader(0.8)
      ->setFooter(0.6);

    $sheet->getPageSetup()
      ->setPaperSize(PageSetup::PAPERSIZE_A4)
      ->setScale(85);
  }

  private function fillWithProducts(Worksheet $sheet)
  {
    $filaInicio = 8;
    $totalProductos = count($this->products);
    $celda = [
      "descripcion" => "C",
      "cantidad" => "E",
      "medida" => "F",
      "precio_unitario" => "G",
      "total" => "H",
    ];
    for ($i = 0; $i < 30; $i++) {
      $numFila = $filaInicio + $i;

      // $sheet->setCellValue("A{$numFila}", $i + 1);

      if ($i < $totalProductos) {
        $prod = $this->products[$i];

        $sheet->setCellValue("{$celda["descripcion"]}{$numFila}", $prod["descripcion"]);
        $sheet->setCellValue("{$celda["cantidad"]}{$numFila}", $prod["cantidad"]);
        $sheet->setCellValue("{$celda["medida"]}{$numFila}", $prod["medida"]);
        $sheet->setCellValue("{$celda["precio_unitario"]}{$numFila}", $prod["precio_unitario"]);

        // $sheet->setCellValue("G{$numFila}", $prod["total"]);
        $sheet->setCellValue("{$celda["total"]}{$numFila}", "={$celda["cantidad"]}{$numFila}*{$celda["precio_unitario"]}{$numFila}");

        $sheet->getStyle("{$celda["precio_unitario"]}{$numFila}:{$celda["total"]}{$numFila}")
          ->getNumberFormat()
          ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
      }
    }
  }

  public function registerEvents(): array
  {
    return [
      BeforeWriting::class => function (BeforeWriting $event) {
        $rutaPlantilla = storage_path("plantilla_test.xlsx");
        $event->writer->reopen(new LocalTemporaryFile($rutaPlantilla), Excel::XLSX);
        $sheet = $event->writer->getSheetByIndex(0)->getDelegate();

        $this->fillWithProducts($sheet);
        $this->setupPage($sheet);

        return $event->getWriter()->getSheetByIndex(0);
      },
      // AfterSheet::class => function (AfterSheet $event) {
      //   $sheet = $event->sheet->getDelegate();

      //   $this->fillWithProducts($sheet);

      //   $this->setupPage($sheet);
      // }
    ];
  }

  public function styles(Worksheet $sheet): ?array
  {
    return [];
    return [
      "A1:G1" => [
        "font" => ["bold" => true],
        'alignment' => [
          'wrapText' => true,
        ],
        "fill" => [
          'fillType' => Fill::FILL_SOLID,
          'startColor' => ['rgb' => 'C0C0C0']
        ]
      ],
      "A1:G31" => [
        'font' => ['size' => 10, 'name' => 'Calibri'],
        'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_CENTER,
          'vertical' => Alignment::VERTICAL_CENTER,
        ],
        "borders" => [
          'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
          ],
        ],
      ],
      'C2:C31' => [
        'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_LEFT,
        ],
      ]
    ];
  }

  public function columnWidths(): array
  {
    return [];
    return [
      'A' => 3,
      'B' => 9,
      'C' => 62,
      'D' => 7,
      'E' => 10,
      'F' => 9,
      'G' => 10,
    ];
  }
}
