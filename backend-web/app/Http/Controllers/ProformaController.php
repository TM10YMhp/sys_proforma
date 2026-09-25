<?php

namespace App\Http\Controllers;

use App\DTO\ProductDTO;
use App\Exports\ProformaData;
use App\Exports\ProformaExport;
use App\Models\Product;
use App\Models\Proforma;
use App\Http\Requests\StoreProformaRequest;
use App\Http\Requests\UpdateProformaRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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
   * @return void
   */
  public function show(Proforma $proforma)
  {
    //
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
  public function update(UpdateProformaRequest $request, Proforma $proforma)
  {
    $validated = $request->validated();
    $proforma->update($validated);
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
   * @return \Illuminate\Http\Response
   */
  public function exportExcel(Proforma $proforma)
  {
    $proforma->load("products");

    /** @var Collection<int, Product> */
    $products = $proforma->products;
    $productsDTO = $products->map(ProductDTO::fromModel(...));
    // dd($productsDTO);

    // return Excel::download(new ProformaExport($productsDTO), "test.xlsx");

    $data = [
      "id" => ProformaData::$id,
      "cliente" => ProformaData::$cliente,
      "productos" => ProformaData::$products,
      "condiciones" => ProformaData::$condiciones,
    ];

    $response = Http::post(
      'http://localhost:5138/api/proforma/excel',
      $this->convertKeyToPascalCase($data)
    );

    return response($response->body(), $response->status())
      ->withHeaders($response->headers());
  }

  /**
   * @return string
   */
  public function exportPDF()
  {
    return "WIP";

    // // TODO: probar mpdf
    // $response = Excel::download(new ProformaExport, "test.pdf", \Maatwebsite\Excel\Excel::MPDF);

    // $response->headers->set('Content-Disposition', 'inline; filename="test.pdf"');

    // return $response;
  }
}
