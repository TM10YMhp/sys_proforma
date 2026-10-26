<?php

namespace App\Modules\Shared\Domain;

/**
 * @template T
 */
class PaginationEntity
{
  /**
   * @param array<T> $data
   * @param array<PaginationLinkEntity> $links
   */
  public function __construct(
    public readonly array $data,
    public readonly int|null $from,
    public readonly array $links,
    public readonly string|null $next_page_url,
    public readonly string|null $prev_page_url,
    public readonly int|null $to,
    public readonly int $total,
  ) {
  }
}