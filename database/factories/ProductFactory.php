<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
  private static int $index = 1;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    // $codigo = "P-" . str_pad((string) self::$index++, 4, '0', STR_PAD_LEFT);
    $codigo = \sprintf("P-%04d", self::$index++);

    return [
      'codigo' => $codigo,
      'descripcion' => fake()->unique()->sentence(),
      'precio' => fake()->randomFloat(2, 0, 100),
      'unidad_medida' => fake()->randomElement(['KG', 'UN']),
      'stock' => fake()->randomDigit(),
      'activo' => fake()->boolean(75)
    ];
  }
}
