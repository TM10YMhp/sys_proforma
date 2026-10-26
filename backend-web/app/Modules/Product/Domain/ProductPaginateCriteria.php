<?php

namespace App\Modules\Product\Domain;

use InvalidArgumentException;

class ProductPaginateCriteria
{
  public function __construct(
    public readonly int $perPage,
    public readonly SortCriteria $sort
  ) {
  }
}

class SortCriteria
{
  public readonly string $direction;

  private const allowedFields = ['codigo', 'nombre', 'precio', 'created_at'];
  private const allowedDirections = ['asc', 'desc'];

  public function __construct(
    public readonly string $field = 'codigo',
    string $direction = 'desc',
  ) {
    $this->direction = strtolower(trim($direction));
    $this->validate();
  }

  private function validate(): void
  {
    if (!\in_array($this->field, self::allowedFields, true)) {
      throw new InvalidArgumentException(
        "No está permitido ordenar por el campo: {$this->field}"
      );
    }

    if (!\in_array($this->direction, self::allowedDirections, true)) {
      throw new InvalidArgumentException("Dirección de ordenamiento inválida, use ASC o DESC");
    }
  }
}