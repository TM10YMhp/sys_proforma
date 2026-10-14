<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Product::factory(20)->create();

    $productos = [
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 30 cm x 22 cm',
        'precio' => 10,
      ],
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 60 cm x 30 cm',
        'precio' => 24,
      ],
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 30 cm x 30 cm',
        'precio' => 12.5,
      ],
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 30 cm x 25 cm',
        'precio' => 10,
      ],
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 100 cm x 60 cm',
        'precio' => 65,
      ],
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 40 cm x 30 cm',
        'precio' => 15,
      ],
      [
        'descripcion' => 'Cartel vinil adhesivo 15 cm x 21 cm',
        'precio' => 2.5,
      ],
      [
        'descripcion' => 'Cartel vinil adhesivo 25 cm x 10 cm',
        'precio' => 3.5,
      ],
      [
        'descripcion' => 'Cartel vinil adhesivo 20 cm x 20 cm',
        'precio' => 4,
      ],
      [
        'descripcion' => 'Cartel vinil adhesivo A4',
        'precio' => 5,
      ],
      [
        'descripcion' => 'Cartel vinil en base celtex 3 mm de 30 cm x 20 cm',
        'precio' => 9,
      ],
    ];

    $datos = collect($productos)->transform(function ($product, $key) {
      $codigo = \sprintf("P-%04d", $key + 1);

      $product['codigo'] = $codigo;
      $product['unidad_medida'] = 'UN';
      $product['stock'] = 50;
      $product['activo'] = true;
      $product['created_at'] = now();
      $product['updated_at'] = now();
      return $product;
    })->all();

    // DB::table('products')->insert($datos);
    Product::insert($datos);
  }
}
