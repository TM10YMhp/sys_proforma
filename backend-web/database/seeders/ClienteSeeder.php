<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ClienteSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Cliente::factory(10)->create();

    $clientes = Collection::times(10, fn() => [
      'nombres' => null,
      'apellido_primario' => null,
      'apellido_secundario' => null,
      'ruc' => null,
      'dni' => null,
      'telefono' => fake()->unique()->numerify('9########'),
      'created_at' => now(),
      'updated_at' => now(),
    ])->transform(function ($cliente) {
      $esEmpresa = fake()->boolean();
      if ($esEmpresa) {
        $cliente['nombres'] = fake()->unique()->company();
        $cliente['ruc'] = fake()->unique()->numerify('20#########');
      } else {
        $cliente['nombres'] = fake()->unique()->name();
        $cliente['apellido_primario'] = fake()->unique()->lastName();
        $cliente['apellido_secundario'] = fake()->unique()->lastName();
        $cliente['dni'] = fake()->unique()->numerify('########');
      }

      return $cliente;
    })->all();

    Cliente::insert($clientes);
  }
}
