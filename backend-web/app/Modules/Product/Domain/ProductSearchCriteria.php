<?php

namespace App\Modules\Product\Domain;

use InvalidArgumentException;

class ProductSearchCriteria
{
  public readonly string $term;

  private const allowedColumns = ['codigo', 'descripcion'];

  public function __construct(
    string $term,
    public readonly string $searchBy,
  ) {
    $this->term = trim($term);
    $this->validate();
  }

  private function validate(): void
  {
    if (!\in_array($this->searchBy, self::allowedColumns, true)) {
      throw new InvalidArgumentException(
        "No está permitido buscar por la columna: {$this->searchBy}"
      );
    }
  }
}