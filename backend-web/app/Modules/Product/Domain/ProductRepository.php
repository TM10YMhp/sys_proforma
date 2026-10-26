<?php

namespace App\Modules\Product\Domain;

use App\Modules\Shared\Domain\PaginationEntity;

interface ProductRepository
{
  public function create(ProductEntity $product): ProductEntity;

  public function update(ProductEntity $product): bool;

  public function delete(int $id): bool;

  public function get(int $id): ProductEntity|null;

  // public function matching(Criteria $criteria): ProductEntity[];

  public function findLatestCode(): string|null;

  /**
   * @return string[]
   */
  public function searchColumnValues(ProductSearchCriteria $criteria): array;

  public function findByField(ProductSearchCriteria $criteria): ProductEntity|null;

  public function getPaginated(ProductPaginateCriteria $criteria): PaginationEntity;
}