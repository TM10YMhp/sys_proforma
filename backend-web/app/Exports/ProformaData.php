<?php

namespace App\Exports;

class ProformaData
{
  public static string $id = "001 - 2547";
  /**
   * @var array{empresa: string, cliente: string, condicion_pago: string}
   */
  public static array $cliente = [
    "empresa" => "Safresco Peru SAC",
    "cliente" => "Paul Sanchez",
    "condicion_pago" => "Credito 15 dias",
  ];
  /**
   * @var array{tiempo_fabricacion: string, validez_oferta: string}
   */
  public static array $condiciones = [
    "tiempo_fabricacion" => "2 dias",
    "validez_oferta" => "7 dias"
  ];
  /* #region products */
  /**
   * @var array{descripcion: string, cantidad: int, medida: string, precio_unitario: float, total: int}[]
   */
  public static array $products = [
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
}
