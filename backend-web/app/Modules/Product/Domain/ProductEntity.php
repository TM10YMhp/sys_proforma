<?php

namespace App\Modules\Product\Domain;

class ProductEntity
{
  public function __construct(
    public int $id,
    public string $codigo,
    public string $descripcion,
    public int $stock,
    public int $precio,
    public string $unidad_medida,
    public bool $activo
  ) {
  }
}