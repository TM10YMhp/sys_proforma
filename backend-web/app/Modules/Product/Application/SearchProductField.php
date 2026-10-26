<?php

namespace App\Modules\Product\Application;

use App\Modules\Product\Domain\ProductRepository;
use App\Modules\Product\Domain\ProductSearchCriteria;

class SearchProductField
{
  public function __construct(
    private readonly ProductRepository $productRepository
  ) {
  }

  public function execute(ProductSearchCriteria $criteria): array
  {
    return $this->productRepository->searchColumnValues($criteria);
  }
}