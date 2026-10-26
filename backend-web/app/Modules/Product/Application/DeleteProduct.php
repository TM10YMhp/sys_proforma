<?php

namespace App\Modules\Product\Application;

use App\Modules\Product\Domain\ProductRepository;

class DeleteProduct
{
  public function __construct(
    private readonly ProductRepository $productRepository
  ) {
  }

  public function execute(int $id)
  {
    $this->productRepository->delete($id);
  }
}