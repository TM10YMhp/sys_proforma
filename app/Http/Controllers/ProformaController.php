<?php

namespace App\Http\Controllers;

use App\DTO\ProductDTO;
use App\Exports\ProformaExport;
use App\Models\Product;
use App\Models\Proforma;
use App\Http\Requests\StoreProformaRequest;
use App\Http\Requests\UpdateProformaRequest;
use Illuminate\Database\Eloquent\Collection;
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
    $proformas = Proforma::with('products')->orderByDesc('created_at')->paginate(6);
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
    return Inertia::render('proformas/create');
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

  /**
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function exportExcel(Proforma $proforma)
  {
    $proforma->load("products");

    /** @var Collection<int, Product> */
    $products = $proforma->products;
    $productsDTO = $products->map(ProductDTO::fromModel(...));
    // dd($productsDTO);

    return Excel::download(new ProformaExport($productsDTO), "test.xlsx");
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
