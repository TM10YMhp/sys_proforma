<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('clientes', function (Blueprint $table) {
      $table->id();

      $table->string('nombres');
      $table->string('apellido_primario')->nullable();
      $table->string('apellido_secundario')->nullable();
      $table->string('ruc')->unique()->nullable();
      $table->string('dni')->unique()->nullable();
      $table->string('telefono')->unique()->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('clientes');
  }
};
