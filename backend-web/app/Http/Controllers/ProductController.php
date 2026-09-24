<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   * @return \Inertia\Response
   */
  public function index()
  {
    $products = Product::orderByDesc('codigo')->paginate(6);
    return Inertia::render("products/index", [
      'products' => $products
    ]);
  }

  /**
   * Show the form for creating a new resource.
   * @return \Inertia\Response
   */
  public function create()
  {
    $ultimoProducto = Product::latest("codigo")->first();
    [$prefijo, $numero] = sscanf($ultimoProducto->codigo, "%[A-Za-z]-%[0-9]");
    $siguienteCodigo = \sprintf("%s-%04d", $prefijo, (int) $numero + 1);

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
  public function update(UpdateProductRequest $request, Product $product)
  {
    $validated = $request->validated();
    $product->update($validated);
    return to_route('products.index');
  }

  /**
   * Remove the specified resource from storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Product $product)
  {
    // TODO: revisar esto
    $product->delete();

    return to_route('products.index');
  }

  public function search(Request $request): JsonResponse
  {
    // TODO: probar laravel/scout
    $buscar = $request->input('q');

    if (empty($buscar)) {
      return response()->json([]);
    }

    $buscar = strtolower($buscar);
    $buscar = str_split(str_replace(" ", "", $buscar));
    $buscar = implode("%", $buscar);
    $buscar = "%" . $buscar . "%";
    $products = Product::whereRaw('lower(codigo) LIKE ?', [$buscar])
      ->select('id', 'codigo', 'precio')
      ->limit(10) // Limitar para mejorar el rendimiento
      // ->dd();
      ->get();

    return response()->json($products);
  }

  public function getByCode(string $code): JsonResponse {
    $product = Product::where('codigo', '=', $code)->first();
    return response()->json($product);
  }
}
