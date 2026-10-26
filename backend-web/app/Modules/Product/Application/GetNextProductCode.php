<?php

namespace App\Modules\Product\Application;

use App\Modules\Product\Domain\ProductRepository;

class GetNextProductCode
{
  public function __construct(
    private readonly ProductRepository $productRepository
  ) {
  }

  public function execute(): string
  {
    $latestCode = $this->productRepository->findLatestCode();
    if (!$latestCode) {
      return "P-0001";
    }

    [$prefijo, $numero] = sscanf($latestCode, "%[A-Za-z]-%[0-9]");
    return \sprintf("%s-%04d", $prefijo, (int) $numero + 1);
  }
}