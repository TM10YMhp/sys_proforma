<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
  'codigo',
  'fecha_emision',
  'fecha_vencimiento',
  'subtotal',
  'igv_tasa',
  'igv_monto',
  'total'
)]
class Proforma extends Model
{
  /** @use HasFactory<\Database\Factories\ProformaFactory> */
  use HasFactory;

  protected function casts(): array
  {
    // https://laravel.com/framework/docs/13.x/eloquent-mutators#date-casting
    return [
      'fecha_emision' => 'datetime',
      'fecha_vencimiento' => 'datetime',
    ];
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, "proforma_items");
  }
}
