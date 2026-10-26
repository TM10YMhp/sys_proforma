<?php

namespace App\Modules\Product\Infrastructure;

use App\Models\Product;
use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductPaginateCriteria;
use App\Modules\Product\Domain\ProductRepository;
use App\Modules\Product\Domain\ProductSearchCriteria;
use App\Modules\Shared\Domain\PaginationEntity;
use App\Modules\Shared\Domain\PaginationLinkEntity;

class EloquentProductRepository implements ProductRepository
{
  public function create(ProductEntity $product): ProductEntity
  {
    $productModel = Product::create([
      'codigo' => $product->codigo,
      'descripcion' => $product->descripcion,
      'precio' => $product->precio,
      'unidad_medida' => $product->unidad_medida,
      'stock' => $product->stock,
      'activo' => $product->activo
    ]);

    return ProductMapper::toDomain($productModel);
  }

  public function update(ProductEntity $product): bool
  {
    $productModel = Product::find($product->id);

    return $productModel->update([
      'codigo' => $product->codigo,
      'descripcion' => $product->descripcion,
      'precio' => $product->precio,
      'unidad_medida' => $product->unidad_medida,
      'stock' => $product->stock,
      'activo' => $product->activo
    ]);
  }

  public function delete(int $id): bool
  {
    $productModel = Product::find($id, ['id']);

    // dd("test: " . json_encode($productModel));

    return $productModel->delete();
  }

  public function get(int $id): ProductEntity|null
  {
    $productModel = Product::find($id, ['*']);

    return ProductMapper::toDomain($productModel);
  }

  public function findLatestCode(): string|null
  {
    $latestProduct = Product::latest("codigo")->first();

    return $latestProduct?->codigo;
  }

  public function searchColumnValues(ProductSearchCriteria $criteria): array
  {
    $limit = 10;
    $column = $criteria->searchBy;

    $rawTerm = $criteria->term;

    $term = strtolower($rawTerm);
    $term = str_replace(" ", "", $term);
    $term = str_split($term);
    $term = implode("%", $term);
    $sqlTerm = "%{$term}%";

    // TODO: esto podria causar inyeccion sql
    /** @phpstan-ignore argument.type */
    $products = Product::whereRaw("lower({$column}) LIKE ?", [$sqlTerm])
      ->select($column)
      ->limit($limit) // Limitar para mejorar el rendimiento
      // ->dd();
      // ->get();
      ->pluck($column)
      ->toArray();

    return $products;
  }

  public function findByField(ProductSearchCriteria $criteria): ProductEntity|null
  {
    $column = $criteria->searchBy;
    $value = $criteria->term;

    $productModel = Product::where($column, '=', $value)->first();
    if (!$productModel) {
      return null;
    }

    return ProductMapper::toDomain($productModel);
  }

  public function getPaginated(ProductPaginateCriteria $criteria): PaginationEntity
  {
    $paginator = Product::orderBy($criteria->sort->field, $criteria->sort->direction)
      ->paginate($criteria->perPage);

    $products = array_map(ProductMapper::toDomain(...), $paginator->items());
    $links = $paginator->linkCollection()->map(fn($link) => new PaginationLinkEntity(
      url: $link["url"],
      label: $link["label"],
      active: $link["active"],
      page: $link["page"]
    ))->toArray();

    return new PaginationEntity(
      data: $products,
      from: $paginator->firstItem(),
      links: $links,
      next_page_url: $paginator->nextPageUrl(),
      prev_page_url: $paginator->previousPageUrl(),
      to: $paginator->lastItem(),
      total: $paginator->total()
    );
  }
}