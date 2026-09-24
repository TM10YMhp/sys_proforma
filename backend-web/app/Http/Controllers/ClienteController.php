<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

  /**
   * @return \Illuminate\Http\JsonResponse
   */
  public function getByRUC(string $ruc)
  {
    // NOTE: mantener en cache por 60 segundos para evitar saturar la api,
    // considerar usar redis
    $cachedValue = Cache::get($ruc);
    if ($cachedValue) {
      return response()->json([
        'razon_social' => $cachedValue
      ]);
    }

    // NOTE: primero buscar en la base de datos local
    $clientePorRUC = Cliente::where('ruc', '=', $ruc)->first();
    if ($clientePorRUC) {
      Cache::put($ruc, $clientePorRUC->nombres, 60);
      return response()->json([
        'razon_social' => $clientePorRUC->nombres
      ]);
    }

    $url = "https://openruc.com/api/ruc/{$ruc}";
    $response = Http::withoutVerifying()->get($url);

    $datos = $response->json();
    if ($response->successful()) {
      Cache::put($ruc, $datos['razon_social'], 60);
      return response()->json([
        'razon_social' => $datos['razon_social']
      ], $response->status());
    }

    return response()->json($datos, $response->status());

    // $ch = curl_init();

    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // /** @var string $response */
    // $response = curl_exec($ch);
    // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // curl_close($ch);

    // if ($http_code === 200) {
    //   return response()->json(json_decode($response, true));
    // } else {
    //   return response()->json([
    //     "mensaje" => "No se pudo conectar con el servicio o el RUC es invalido. Codigo HTTP: {$http_code}"
    //   ]);
    // }
  }
}
