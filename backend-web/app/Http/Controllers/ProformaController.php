<?php

namespace App\Http\Controllers;

use App\Exports\ProformaData;
use App\Models\Product;
use App\Models\Proforma;
use App\Http\Requests\StoreProformaRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProformaController extends Controller
{
  /**
   * Display a listing of the resource.
   * @return \Inertia\Response
   */
  public function index()
  {
    $proformas = Proforma::with('products')->orderByDesc('codigo')->paginate(6);
    return Inertia::render("proformas/index", [
      'proformas' => $proformas
    ]);
  }

  /**
   * Show the form for creating a new resource.
   * @return \Inertia\Response
   */
  public function create()
  {
    $ultimaProforma = Proforma::latest("codigo")->first();
    [$prefijo, $numero] = sscanf($ultimaProforma->codigo, "%[0-9] - %[0-9]");
    $siguienteCodigo = \sprintf("001 - %04d", (int) $numero + 1);
    return Inertia::render('proformas/create', [
      'codigo' => $siguienteCodigo
    ]);
  }

  /**
   * Store a newly created resource in storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(StoreProformaRequest $request)
  {
    $validated = $request->validated();
    Proforma::create($validated);
    return to_route("proformas.index");
  }

  /**
   * Display the specified resource.
   * @return \Inertia\Response
   */
  public function show(Proforma $proforma)
  {
    $proforma->load('products');
    return Inertia::render("proformas/show", [
      'proforma' => $proforma
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   * @return \Inertia\Response
   */
  public function edit(Proforma $proforma)
  {
    $proforma->load("products");
    return Inertia::render('proformas/edit', [
      'proforma' => $proforma
    ]);
  }

  /**
   * Update the specified resource in storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, Proforma $proforma)
  {
    // TODO: se quito la validacion por falta de tiempo
    // $validated = $request->validated();

    $validated = $request->validate([
      'fecha_emision' => 'required',
      'fecha_vencimiento' => 'required',
      'subtotal' => 'required|numeric',
      'igv_tasa' => 'required|numeric',
      'productos' => 'required|array',
      'productos.*.id' => 'required|exists:products,id',
      'productos.*.cantidad' => 'required|integer|min:1',
    ]);

    $productIds = collect($validated['productos'])->pluck('id')->all();
    $products = Product::whereIn('id', $productIds)->get();
    $productsIndexed = $products->keyBy('id');

    DB::transaction(function () use ($proforma, $validated, $productsIndexed) {
      $igvMonto = $validated['subtotal'] * $validated['igv_tasa'];
      $proforma->update([
        'fecha_emision' => $validated['fecha_emision'],
        'fecha_vencimiento' => $validated['fecha_vencimiento'],
        'subtotal' => $validated['subtotal'],
        'igv_tasa' => $validated['igv_tasa'],
        'igv_monto' => $igvMonto,
        'total' => $validated['subtotal'] + $igvMonto,
      ]);

      // NOTE: formato requerido [ id_producto => [columna_pivote => valor] ]
      $productosPivot = [];
      foreach ($validated['productos'] as $item) {
        $prod = $productsIndexed->get($item['id']);

        // NOTE: quizas el id no exista
        if (!$prod) {
          continue;
        }

        $productosPivot[$item['id']] = [
          'cantidad' => $item['cantidad'],
          'precio_unitario' => $prod->precio,
          'subtotal' => $prod->precio * $item['cantidad'],
        ];
      }

      $proforma->products()->sync($productosPivot);
    });

    return to_route('proformas.index');
  }

  /**
   * Remove the specified resource from storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Proforma $proforma)
  {
    // TODO: revisar esto
    $proforma->delete();

    return to_route('proformas.index');
  }

  private function convertKeyToPascalCase(mixed $datos): mixed
  {
    // caso base de la recursion
    if (!\is_array($datos)) {
      return $datos;
    }

    $resultado = [];
    foreach ($datos as $llave => $valor) {
      $nuevaLlave = \is_string($llave) ? Str::studly($llave) : $llave;
      // recursion
      $resultado[$nuevaLlave] = $this->convertKeyToPascalCase($valor);
    }

    return $resultado;
  }

  /**
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function exportExcel(Proforma $proforma)
  {
    $proforma->load("products");

    $productos = $proforma->products->map(fn($it) => [
      "descripcion" => $it->descripcion,
      "cantidad" => $it->pivot->cantidad,
      "medida" => $it->unidad_medida,
      "precio_unitario" => $it->precio,
    ])->toArray();

    $data = $this->convertKeyToPascalCase([
      "id" => $proforma->codigo,
      "cliente" => ProformaData::$cliente,
      "productos" => $productos,
      "condiciones" => ProformaData::$condiciones,
      "fecha_emision" => $proforma->fecha_emision,
      "fecha_vencimiento" => $proforma->fecha_vencimiento,
    ]);

    try {
      $response = Http::post(
        'http://localhost:5138/api/proforma/excel',
        $data
      );

      return response($response->body(), $response->status())
        ->withHeaders($response->headers());
    } catch (ConnectionException $e) {
      return response()->json([
        'error' => 'El servicio de la API no esta disponible.'
      ], 503);
    }
  }

  /**
   * NOTE: es necesario que el excel se haya creado antes
   * TODO: pasar datos de la proforma y manejar el estado interno
   * TODO: verificar que la webapi este activa
   * @return \Illuminate\Http\Response
   */
  public function exportPDF(string $id)
  {
    $response = Http::post("http://localhost:5138/api/proforma/pdf/{$id}");

    return response($response->body(), $response->status())
      ->withHeaders($response->headers())
      ->header('Content-Type', 'application/pdf')
      ->header('Content-Disposition', "inline; filename=\"proforma_{$id}.pdf\"");
  }

  /**
   * @return \Illuminate\Http\Response
   */
  public function getImgById(string $id)
  {
    $response = Http::get("http://localhost:5138/api/proforma/img/{$id}");

    return response($response->body(), $response->status())
      ->withHeaders($response->headers());
  }
}
