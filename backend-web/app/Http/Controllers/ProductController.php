<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Application\DeleteProduct;
use App\Modules\Product\Application\FindProductByField;
use App\Modules\Product\Application\GetNextProductCode;
use App\Modules\Product\Application\GetPaginatedProducts;
use App\Modules\Product\Application\SearchProductField;
use App\Modules\Product\Application\UpdateProduct;
use App\Modules\Product\Domain\ProductPaginateCriteria;
use App\Modules\Product\Domain\ProductSearchCriteria;
use App\Modules\Product\Domain\SortCriteria;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   * @return \Inertia\Response
   */
  public function index(GetPaginatedProducts $getPaginatedProducts)
  {
    // $products = Product::orderByDesc('codigo')->paginate(6);
    $products = $getPaginatedProducts->execute(new ProductPaginateCriteria(
      perPage: 6,
      sort: new SortCriteria(
        field: 'codigo',
        direction: 'desc'
      )
    ));
    return Inertia::render("products/index", [
      'products' => $products
    ]);
  }

  /**
   * Show the form for creating a new resource.
   * @return \Inertia\Response
   */
  public function create(GetNextProductCode $getNextProductCode)
  {
    $siguienteCodigo = $getNextProductCode->execute();

    return Inertia::render('products/create', [
      'codigo' => $siguienteCodigo
    ]);
  }

  /**
   * Store a newly created resource in storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(StoreProductRequest $request)
  {
    $validated = $request->validated();
    Product::create($validated);
    return to_route("products.index");
  }

  /**
   * Display the specified resource.
   * @return void
   */
  public function show(Product $product)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   * @return \Inertia\Response
   */
  public function edit(Product $product)
  {
    return Inertia::render('products/edit', [
      'product' => $product
    ]);
  }

  /**
   * Update the specified resource in storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(
    UpdateProductRequest $request,
    int $id,
    UpdateProduct $updateProduct
  ) {
    $validated = $request->validated();

    $updateProduct->execute($id, $validated);

    return to_route('products.index');
  }

  /**
   * Remove the specified resource from storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(int $id, DeleteProduct $deleteProduct)
  {
    $deleteProduct->execute($id);

    return to_route('products.index');
  }

  public function search(
    Request $request,
    SearchProductField $searchProductField
  ): JsonResponse {
    // TODO: probar laravel/scout
    // $buscar = $request->input('q');
    $buscar = $request->query('q');
    $by = $request->query('by', 'codigo');

    if (empty($buscar)) {
      return response()->json([]);
    }

    try {
      $products = $searchProductField->execute(new ProductSearchCriteria(
        term: $buscar,
        searchBy: $by
      ));

      return response()->json($products);
    } catch (Exception $e) {
      return response()->json(['error' => $e->getMessage()], 422);
    }
  }

  public function get(Request $request, FindProductByField $findProductByField): JsonResponse
  {
    $buscar = $request->query('q');
    $by = $request->query('by', 'codigo');

    if (empty($buscar)) {
      return response()->json((object) []);
    }

    $product = $findProductByField->execute(
      new ProductSearchCriteria(
        term: $buscar,
        searchBy: $by
      )
    );
    if (!$product) {
      return response()->json(null, 404);
    }

    return response()->json($product);
  }
}
