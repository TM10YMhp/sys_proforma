<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Proforma;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proforma>
 */
class ProformaFactory extends Factory
{
  private static int $index = 2547;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $codigo = \sprintf("001 - %d", self::$index++);

    // https://fakerphp.org/formatters/numbers-and-strings
    return [
      // 'codigo' => fake()->unique()->numerify('001 - ####'),
      'codigo' => $codigo,
      'fecha_emision' => fake()->dateTimeThisYear(),
      'fecha_vencimiento' => fake()->dateTimeThisYear(),
      'subtotal' => fake()->randomFloat(2, 0, 100),
      // 'igv_tasa' => fake()->randomFloat(2, 0, 100),
      'igv_monto' => fake()->randomFloat(2, 0, 100),
      'total' => fake()->randomFloat(2, 0, 100)
    ];
  }

  // public function configure()
  // {
  //   return $this->afterCreating(function (Proforma $proforma) {
  //     $productIds = Product::inRandomOrder()
  //       ->take(fake()->randomElement([8, 15, 23]))
  //       ->pluck('id');

  //     foreach ($productIds as $id) {
  //       $proforma->products()->attach($id, [
  //         'cantidad' => fake()->randomDigit() + 3,
  //         'precio_unitario' => fake()->randomFloat(2, 0, 100),
  //         'subtotal' => fake()->randomFloat(2, 0, 100),
  //       ]);
  //     }
  //   });
  // }
}
