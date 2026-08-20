<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'nombre' => fake()->unique()->word(),
      'descripcion' => fake()->unique()->sentence(),
      'precio' => fake()->randomFloat(2, 0, 100),
      'unidad_medida' => fake()->randomElement(['KG', 'UN']),
      'stock' => fake()->randomDigit(),
      'activo' => fake()->boolean(75)
    ];
  }
}
