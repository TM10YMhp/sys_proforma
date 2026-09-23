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
    $clientes = Cliente::orderByDesc('updated_at')->paginate(6);
    return Inertia::render("clientes/index", [
      'clientes' => $clientes
    ]);
  }

  /**
   * Show the form for creating a new resource.
   * @return \Inertia\Response
   */
  public function create()
  {
    return Inertia::render('clientes/create');
  }

  /**
   * Store a newly created resource in storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(StoreClienteRequest $request)
  {
    $validated = $request->validated();
    Cliente::create($validated);
    return to_route("clientes.index");
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
   * @return \Inertia\Response
   */
  public function edit(Cliente $cliente)
  {
    return Inertia::render('clientes/edit', [
      'cliente' => $cliente
    ]);
  }

  /**
   * Update the specified resource in storage.
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(UpdateClienteRequest $request, Cliente $cliente)
  {
    $validated = $request->validated();
    $cliente->update($validated);
    return to_route('clientes.index');
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
