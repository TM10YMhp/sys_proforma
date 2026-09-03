<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Proforma;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create();

    User::factory()->create([
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);

    // $this->call([
    //   ProductSeeder::class,
    //   ProformaSeeder::class,
    // ]);

    $products = Product::factory(30)->create();

    Proforma::factory(20)->hasAttached(
      $products->random(fake()->randomElement([8, 15])),
      fn() => [
        'cantidad' => fake()->randomDigit() + 3,
        'precio_unitario' => fake()->randomFloat(2, 5, 100),
        'subtotal' => fake()->randomFloat(2, 5, 100),
      ]
    )->create();
  }
}
