<?php

namespace App\DTO;

use App\Models\Product;

class ProductDTO
{
  public function __construct(
    public string $descripcion,
    public int $cantidad,
    public string $medida,
    public float $precio_unitario,
    public int $total
  ) {
  }

  public static function fromModel(Product $product): ProductDTO
  {
    return new ProductDTO(
      descripcion: $product->descripcion,
      cantidad: $product->stock,
      medida: $product->unidad_medida,
      precio_unitario: $product->precio,
      total: 0,
    );
  }
}