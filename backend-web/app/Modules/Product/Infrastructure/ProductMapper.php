<?php

namespace App\Modules\Product\Infrastructure;

use App\Models\Product;
use App\Modules\Product\Domain\ProductEntity;

class ProductMapper
{
  public static function toDomain(Product $eloquent): ProductEntity
  {
    return new ProductEntity(
      id: $eloquent->id,
      codigo: $eloquent->codigo,
      descripcion: $eloquent->descripcion,
      stock: $eloquent->stock,
      precio: $eloquent->precio,
      unidad_medida: $eloquent->unidad_medida,
      activo: $eloquent->activo
    );
  }
}