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
  private string $baseUrl;

  public function __construct()
  {
    $url = config('services.proforma.url');
    if (!$url) {
      abort(404, 'El servicio generador no esta establecido');
    }

    $this->baseUrl = rtrim($url, "/");
  }


  /**
   * Display a listing of the resource.
   * @return \Inertia\Response
   */
  public function index()
  {
    $proformas = Proforma::with(['products', 'cliente'])
      ->orderByDesc('codigo')->paginate(10);
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
  public function store(Request $request)
  {
    // TODO: se quito la validacion por falta de tiempo
    // $validated = $request->validated();
    // Proforma::create($validated);

    $validated = $request->validate([
      'cliente_id' => 'required|exists:clientes,id',
      'codigo' => 'required',
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

    DB::transaction(function () use ($validated, $productsIndexed) {
      $igvMonto = $validated['subtotal'] * $validated['igv_tasa'];
      $proforma = Proforma::create([
        'cliente_id' => $validated['cliente_id'],
        'codigo' => $validated['codigo'],
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

  // /**
  //  * Display the specified resource.
  //  * @return \Inertia\Response
  //  */
  // public function show(Proforma $proforma)
  // {
  //   $proforma->load('products');
  //   return Inertia::render("proformas/show", [
  //     'proforma' => $proforma
  //   ]);
  // }

  /**
   * Show the form for editing the specified resource.
   * @return \Inertia\Response
   */
  public function edit(Proforma $proforma)
  {
    $proforma->load(['products', 'cliente']);

    // $proformaData = array_filter(
    //   $proforma->toArray(),
    //   fn($value) => $value !== null
    // );
    // if (isset($proformaData['cliente'])) {
    //   $proformaData['cliente'] = array_filter(
    //     $proformaData['cliente'],
    //     fn($value) => $value !== null
    //   );
    // }

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
    $validated = $request->validate([
      'cliente_id' => 'required|exists:clientes,id',
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
        'cliente_id' => $validated['cliente_id'],
        'fecha_emision' => $validated['fecha_emision'],
        'fecha_vencimiento' => $validated['fecha_vencimiento'],
        'subtotal' => $validated['subtotal'],
        'igv_tasa' => $validated['igv_tasa'],
        'igv_monto' => $igvMonto,
        'total' => $validated['subtotal'] + $igvMonto,
      ]);

      $existingProducts = $proforma->products->keyBy('id');

      // NOTE: formato requerido [ id_producto => [columna_pivote => valor] ]
      $productosPivot = [];
      foreach ($validated['productos'] as $item) {
        $prod = $productsIndexed->get($item['id']);

        // NOTE: quizas el id no exista
        if (!$prod) {
          continue;
        }

        $existing = $existingProducts->get($item['id']);
        $precioUnitario = $existing ? $existing->pivot->precio_unitario : $prod->precio;

        $productosPivot[$item['id']] = [
          'cantidad' => $item['cantidad'],
          'precio_unitario' => $precioUnitario,
          'subtotal' => $precioUnitario * $item['cantidad'],
        ];
      }

      $proforma->products()->sync($productosPivot);
    });

    // TODO: separar y manejar error
    $data = $this->formatProformaData($proforma);
    $postResponse = Http::post("{$this->baseUrl}/api/proforma/img", $data);

    // TODO: no recarga
    return to_route('proformas.edit', ['proforma' => $proforma->id]);
  }

  /**
   * Remove the specified resource from storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Proforma $proforma)
  {
    // TODO: revisar esto
    $proforma->delete();

    $codigo = str_replace(" ", "_", trim($proforma->codigo));
    Http::delete("{$this->baseUrl}/api/proforma/{$codigo}");

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

  private function formatProformaData(Proforma $proforma)
  {
    $proforma->load(["products", "cliente"]);

    $productos = $proforma->products->map(fn($it) => [
      "descripcion" => $it->descripcion,
      "cantidad" => $it->pivot->cantidad,
      "medida" => $it->unidad_medida,
      "precio_unitario" => $it->pivot->precio_unitario,
    ])->toArray();

    $cliente = [
      "empresa" => "",
      "nombre_cliente" => "",
      "condicion_pago" => "Credito 15 dias",
    ];

    $clienteData = $proforma->cliente;
    if ($clienteData->ruc) {
      $cliente['empresa'] = $clienteData->nombres;
    } else {
      $cliente['nombre_cliente'] = implode(' ', array_filter([
        $clienteData->nombres,
        $clienteData->apellido_primario,
        $clienteData->apellido_secundario
      ]));
    }

    return $this->convertKeyToPascalCase([
      "id" => $proforma->codigo,
      "cliente" => $cliente,
      "productos" => $productos,
      "condiciones" => ProformaData::$condiciones,
      "fecha_emision" => $proforma->fecha_emision,
      "fecha_vencimiento" => $proforma->fecha_vencimiento,
    ]);
  }

  /**
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function exportExcel(Proforma $proforma)
  {
    try {
      $url = config('services.proforma.url');
      if (!$url) {
        return response()->json([
          'error' => 'El servicio generador no esta establecido'
        ], 404);
      }

      $data = $this->formatProformaData($proforma);
      $response = Http::post(
        "{$url}/api/proforma/excel",
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
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function exportPDF(Proforma $proforma)
  {
    try {
      $url = config('services.proforma.url');
      if (!$url) {
        return response()->json([
          'error' => 'El servicio generador no esta establecido'
        ], 404);
      }

      $data = $this->formatProformaData($proforma);
      $response = Http::post(
        "{$url}/api/proforma/pdf",
        $data
      );

      if ($response->successful()) {
        $contentDisposition = $response->header('Content-Disposition');
        $inlineDisposition = str_replace('attachment', 'inline', $contentDisposition);
        return response($response->body(), $response->status())
          ->withHeaders($response->headers())
          ->header('Content-Disposition', $inlineDisposition);
      }

      return response($response->body(), $response->status());
    } catch (ConnectionException $e) {
      return response()->json([
        'error' => 'El servicio de la API no esta disponible.'
      ], 503);
    }
  }

  /**
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function getImgUrl(Proforma $proforma)
  {
    $baseUrl = config('services.proforma.url');
    if (!$baseUrl) {
      return response()->json([
        'error' => 'El servicio generador no esta establecido'
      ], 404);
    }

    $codigo = str_replace(" ", "_", trim($proforma->codigo));
    $imageUrl = "{$baseUrl}/api/proforma/img/{$codigo}";

    try {
      $response = Http::get($imageUrl);
      if ($response->status() === 404) {
        $data = $this->formatProformaData($proforma);
        $postResponse = Http::post("{$baseUrl}/api/proforma/img", $data);
        if ($postResponse->failed()) {
          return response($postResponse->body(), $postResponse->status())
            ->withHeaders($postResponse->headers());
        }
      } elseif ($response->failed()) {
        return response($response->body(), $response->status())
          ->withHeaders($response->headers());
      }

      return response()->json([
        'url' => "/proformas/getImgById/{$codigo}?t={$proforma->updated_at}"
      ]);
    } catch (ConnectionException $e) {
      return response()->json([
        'error' => 'El servicio generador de imágenes no está disponible en este momento.'
      ], 503);
    }
  }

  /**
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function getImgById(string $id)
  {
    $url = config('services.proforma.url');
    if (!$url) {
      return response()->json([
        'error' => 'El servicio generador no esta establecido'
      ], 404);
    }

    $codigo = str_replace(" ", "_", trim($id));
    $response = Http::get("{$url}/api/proforma/img/{$codigo}");

    return response($response->body(), $response->status())
      ->withHeaders($response->headers());
  }

  /**
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function getExcelById(string $id)
  {
    $url = config('services.proforma.url');
    if (!$url) {
      return response()->json([
        'error' => 'El servicio generador no esta establecido'
      ], 404);
    }

    $codigo = str_replace(" ", "_", trim($id));
    $response = Http::get("{$url}/api/proforma/excel/{$codigo}");

    return response($response->body(), $response->status())
      ->withHeaders($response->headers());
  }

  /**
   * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
   */
  public function getPDFById(string $id)
  {
    $codigo = str_replace(" ", "_", trim($id));
    $response = Http::get("{$this->baseUrl}/api/proforma/pdf/{$codigo}");

    if ($response->successful()) {
      $contentDisposition = $response->header('Content-Disposition');
      $inlineDisposition = str_replace('attachment', 'inline', $contentDisposition);
      return response($response->body(), $response->status())
        ->withHeaders($response->headers())
        ->header('Content-Disposition', $inlineDisposition);
    }

    return response($response->body(), $response->status())
      ->withHeaders($response->headers());
  }
}
