<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
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
    $url = "https://openruc.com/api/ruc/{$ruc}";

    $response = Http::withoutVerifying()->get($url);

    if ($response->successful()) {
      $datos = $response->json();

      // NOTE: la api devuelve 200 en caso de error, con esto devolvera el codigo correcto
      if (isset($datos['error'])) {
        return response()->json($datos, 404);
      }

      return response()->json($datos);
    }

    return response()->json([
      'error' => 'desconocido',
      'mensaje' => 'Servidor de consultas no disponible temporalmente.'
    ], 500);


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
