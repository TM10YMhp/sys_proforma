<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Inertia\Inertia;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   * @return \Inertia\Response
   */
  public function index()
  {
    $products = Product::orderByDesc('created_at')->paginate(6);
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
    return Inertia::render('products/create');
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
}
