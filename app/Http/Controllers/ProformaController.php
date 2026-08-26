<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Http\Requests\StoreProformaRequest;
use App\Http\Requests\UpdateProformaRequest;
use Inertia\Inertia;

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
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreProformaRequest $request)
  {
    //
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
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateProformaRequest $request, Proforma $proforma)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Proforma $proforma)
  {
    //
  }
}
