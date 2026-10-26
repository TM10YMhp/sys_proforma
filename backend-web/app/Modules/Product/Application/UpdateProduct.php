<?php

namespace App\Modules\Product\Application;

use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepository;

class UpdateProduct
{
  public function __construct(
    private readonly ProductRepository $productRepository
  ) {
  }

  public function execute(int $id, array $data)
  {
    $this->productRepository->update(new ProductEntity(
      id: $id,
      codigo: $data['codigo'],
      descripcion: $data['descripcion'],
      precio: $data['precio'],
      unidad_medida: $data['unidad_medida'],
      stock: $data['stock'],
      activo: $data['activo']
    ));
  }
}