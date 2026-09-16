<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Inertia\Inertia;

class ClienteController extends Controller
{
  /**
   * Display a listing of the resource.
   * @return \Inertia\Response
   */
  public function index()
  {
    $clientes = Cliente::orderByDesc('codigo')->paginate(6);
    return Inertia::render("clientes/index", [
      'clientes' => $clientes
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): void
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreClienteRequest $request): void
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Cliente $cliente): void
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Cliente $cliente): void
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateClienteRequest $request, Cliente $cliente): void
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Cliente $cliente)
  {
    // TODO: revisar esto
    $cliente->delete();

    return to_route('clientes.index');
  }
}
