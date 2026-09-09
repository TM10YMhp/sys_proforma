<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(
  'nombre',
  'descripcion',
  'stock',
  'precio',
  'unidad_medida',
  'activo'
)]
class Product extends Model
{
  /** @use HasFactory<\Database\Factories\ProductFactory> */
  use HasFactory;

  protected function casts(): array
  {
    return [
      'activo' => 'boolean',
    ];
  }

  /**
   * @return BelongsToMany<Proforma, $this>
   */
  public function proformas()
  {
    return $this->belongsToMany(Proforma::class, "proforma_items");
  }
}
