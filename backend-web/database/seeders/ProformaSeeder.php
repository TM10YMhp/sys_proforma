<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Proforma;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProformaSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $products = Product::all();

    foreach (range(1, 10) as $i) {
      $randomProducts = $products->random(fake()->numberBetween(2, 5));

      $totalProforma = 0;
      $pivoteDatos = [];

      $randomProducts->each(function ($product) use (&$totalProforma, &$pivoteDatos) {
        $cantidad = fake()->randomDigit() + 3;
        $precioUnitario = $product->precio;
        $subtotal = round($cantidad * $precioUnitario, 2);

        $totalProforma += $subtotal;

        $pivoteDatos[$product->id] =
          [
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
          ];
      });

      $subtotalProforma = round($totalProforma, 2);
      $montoIgv = $subtotalProforma * 0.18;
      $codigo = \sprintf("001 - %d", 2547 + $i);
      $proforma = Proforma::create([
        'subtotal' => $subtotalProforma,
        'codigo' => $codigo,
        'igv_monto' => $montoIgv,
        'total' => $subtotalProforma + $montoIgv,
        'fecha_emision' => fake()->dateTimeThisYear(),
        'fecha_vencimiento' => fake()->dateTimeThisYear()
      ]);

      // NOTE: attach tambien acepta array de ids con valor
      // https://laravel.com/framework/docs/13.x/eloquent-relationships#attaching-detaching
      $proforma->products()->attach($pivoteDatos);
    }

    // NOTE: esto requiere crear proformas vacias y luego actuslizar
    // $proformas->each(function ($proforma) use ($products) {
    //   $randomProducts = $products->random(fake()->numberBetween(2, 5));

    //   $randomProducts->each(function ($product) use ($proforma) {
    //     $cantidad = fake()->randomDigit() + 3;
    //     $precioUnitario = $product->precio;
    //     $subtotal = round($cantidad * $precioUnitario, 2);

    //     $proforma->products()->attach(
    //       $product->id,
    //       [
    //         'cantidad' => $cantidad,
    //         'precio_unitario' => $precioUnitario,
    //         'subtotal' => $subtotal,
    //       ]
    //     );
    //   });

    //   // $proforma->update([
    //   //   'subtotal' => round($totalProforma, 2)
    //   // ]);
    // });
  }
}
