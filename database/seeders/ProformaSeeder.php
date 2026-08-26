<?php

namespace Database\Seeders;

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
    Proforma::factory(20)->create();
  }
}
