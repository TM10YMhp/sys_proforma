<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::prefix('clientes')->group(function () {
  Route::get('/', [ClienteController::class, 'index'])->name('clientes.index');
  Route::get('/create', [ClienteController::class, 'create'])->name('clientes.create');
  Route::post('/', [ClienteController::class, 'store'])->name('clientes.store');
  Route::get('/edit/{cliente}', [ClienteController::class, 'edit'])->name('clientes.edit');
  Route::put('/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
  Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
});
