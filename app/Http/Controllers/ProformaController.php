<?php

namespace App\Http\Controllers;

use App\Exports\ProformaExport;
use App\Models\Proforma;
use App\Http\Requests\StoreProformaRequest;
use App\Http\Requests\UpdateProformaRequest;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ProformaController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $proformas = Proforma::orderByDesc('created_at')->paginate(6);
    return Inertia::render("proformas/index", [
      'proformas' => $proformas
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return Inertia::render('proformas/create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreProformaRequest $request)
  {
    $validated = $request->validated();
    Proforma::create($validated);
    return to_route("proformas.index");
  }

  /**
   * Display the specified resource.
   */
  public function show(Proforma $proforma)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Proforma $proforma)
  {
    return Inertia::render('proformas/edit', [
      'proforma' => $proforma
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateProformaRequest $request, Proforma $proforma)
  {
    $validated = $request->validated();
    $proforma->update($validated);
    return to_route('proformas.index');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Proforma $proforma)
  {
    // TODO: revisar esto
    $proforma->delete();

    return to_route('proformas.index');
  }

  public function exportExcel()
  {
    return Excel::download(new ProformaExport, "test.xlsx");
  }

  public function exportPDF()
  {
    // TODO: probar mpdf
    $response = Excel::download(new ProformaExport, "test.pdf", \Maatwebsite\Excel\Excel::MPDF);

    $response->headers->set('Content-Disposition', 'inline; filename="test.pdf"');

    return $response;
  }
}
