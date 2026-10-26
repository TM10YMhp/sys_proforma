<?php

namespace App\Modules\Product\Application;

use App\Modules\Product\Domain\ProductPaginateCriteria;
use App\Modules\Product\Domain\ProductRepository;
use App\Modules\Shared\Domain\PaginationEntity;

class GetPaginatedProducts
{
  public function __construct(
    private readonly ProductRepository $productRepository
  ) {
  }

  public function execute(ProductPaginateCriteria $criteria): PaginationEntity
  {
    return $this->productRepository->getPaginated($criteria);
  }
}