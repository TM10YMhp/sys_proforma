<?php

namespace App\Modules\Product\Application;

use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepository;
use App\Modules\Product\Domain\ProductSearchCriteria;

class FindProductByField
{
  public function __construct(
    private readonly ProductRepository $productRepository
  ) {
  }

  public function execute(ProductSearchCriteria $criteria): ProductEntity|null
  {
    return $this->productRepository->findByField($criteria);
  }
}