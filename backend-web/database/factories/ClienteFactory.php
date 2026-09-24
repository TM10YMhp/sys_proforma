<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'nombres' => fake()->unique()->name(),
      'apellido_primario' => fake()->unique()->lastName(),
      'apellido_secundario' => fake()->unique()->lastName(),
      'ruc' => fake()->unique()->numerify('20#########'),
      'dni' => fake()->unique()->numerify('########'),
      'telefono' => fake()->e164PhoneNumber()
    ];
  }
}
