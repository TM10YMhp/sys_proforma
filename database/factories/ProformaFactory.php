<?php

namespace Database\Factories;

use App\Models\Proforma;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proforma>
 */
class ProformaFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    // https://fakerphp.org/formatters/numbers-and-strings
    return [
      'codigo' => fake()->unique()->numerify('001-####'),
      'fecha_emision' => fake()->dateTimeThisYear(),
      'fecha_vencimiento' => fake()->dateTimeThisYear(),
      'subtotal' => fake()->randomFloat(2, 0, 100),
      // 'igv_tasa' => fake()->randomFloat(2, 0, 100),
      'igv_monto' => fake()->randomFloat(2, 0, 100),
      'total' => fake()->randomFloat(2, 0, 100)
    ];
  }
}
