<?php

namespace App\Modules\Shared\Domain;

class PaginationLinkEntity
{
  public function __construct(
    public readonly string|null $url,
    public readonly string $label,
    public readonly bool $active,
    public readonly int|null $page,
  ) {
  }
}