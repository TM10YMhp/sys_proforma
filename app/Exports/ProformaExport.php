<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProformaExport implements Export, WithEvents
{
  private $id = "001 - 2547";
  private $cliente = [
    "empresa" => "Safresco Peru SAC",
    "cliente" => "Paul Sanchez",
    "condicion_pago" => "Credito 15 dias",
  ];
  private $condiciones = [
    "tiempo_fabricacion" => "2 dias",
    "validez_oferta" => "7 dias"
  ];
  /* #region products */
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
  /* #endregion */

  /**
   * @param Product[] $products
   */
  public function __construct(array $products)
  {
    $this->products = $products;

    // // TODO: traer datos desde proforma
    // $this->products = Product::take(10)->get()->map(
    //   fn($prod) => [
    //     "descripcion" => $prod->descripcion,
    //     "cantidad" => $prod->stock,
    //     "medida" => $prod->unidad_medida,
    //     "precio_unitario" => $prod->precio,
    //     // NOTE: calculado por excel
    //     "total" => 0,
    //   ]
    // )->toArray();
  }

  private function fillWithProducts(Worksheet $sheet)
  {
    $filaInicio = 8;
    $totalProductos = \count($this->products);
    $celda = [
      "descripcion" => "C",
      "cantidad" => "E",
      "medida" => "F",
      "precio_unitario" => "G",
      "total" => "H",
    ];

    for ($i = 0; $i < 30; $i++) {
      $numFila = $filaInicio + $i;

      if ($i < $totalProductos) {
        $prod = $this->products[$i];

        $sheet->setCellValue("{$celda["descripcion"]}{$numFila}", $prod["descripcion"]);
        $sheet->setCellValue("{$celda["cantidad"]}{$numFila}", $prod["cantidad"]);
        $sheet->setCellValue("{$celda["medida"]}{$numFila}", $prod["medida"]);
        $sheet->setCellValue("{$celda["precio_unitario"]}{$numFila}", $prod["precio_unitario"]);

        // NOTE: calcular el total con formula
        $sheet->setCellValue(
          "{$celda["total"]}{$numFila}",
          "={$celda["cantidad"]}{$numFila}*{$celda["precio_unitario"]}{$numFila}"
        );

        $sheet->getStyle("{$celda["precio_unitario"]}{$numFila}:{$celda["total"]}{$numFila}")
          ->getNumberFormat()
          ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
      }
    }
  }

  private function fillWithId(Worksheet $sheet)
  {
    $sheet->setCellValue("A2", "PROFORMA {$this->id}");
  }


  private function fillWithClient(Worksheet $sheet)
  {
    $sheet->setCellValue("C3", $this->cliente["empresa"]);
    $sheet->setCellValue("C4", $this->cliente["cliente"]);
    $sheet->setCellValue("C5", $this->cliente["condicion_pago"]);

  }

  private function fillWithConditions(Worksheet $sheet)
  {
    $sheet->setCellValue("D41", $this->condiciones["tiempo_fabricacion"]);
    $sheet->setCellValue("D42", $this->condiciones["validez_oferta"]);
  }


  public function registerEvents(): array
  {
    return [
      BeforeWriting::class => function (BeforeWriting $event) {
        // dd($this->products);
        $rutaPlantilla = storage_path("plantilla_test.xlsx");
        $event->writer->reopen(new LocalTemporaryFile($rutaPlantilla), Excel::XLSX);
        $sheet = $event->writer->getSheetByIndex(0)->getDelegate();

        $this->fillWithId($sheet);
        $this->fillWithClient($sheet);
        $this->fillWithProducts($sheet);
        $this->fillWithConditions($sheet);

        return $event->getWriter()->getSheetByIndex(0);
      }
    ];
  }
}
