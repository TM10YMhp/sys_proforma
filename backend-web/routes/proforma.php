<?php

use App\Http\Controllers\ProformaController;
use Illuminate\Support\Facades\Route;

Route::prefix('proformas')->group(function () {
  Route::get('/', [ProformaController::class, 'index'])->name('proformas.index');
  Route::get('/{proforma}', [ProformaController::class, 'show'])->name('proformas.show');
  Route::get('/create', [ProformaController::class, 'create'])->name('proformas.create');
  Route::post('/', [ProformaController::class, 'store'])->name('proformas.store');
  Route::get('/edit/{proforma}', [ProformaController::class, 'edit'])->name('proformas.edit');
  Route::put('/{proforma}', [ProformaController::class, 'update'])->name('proformas.update');
  Route::delete('/{proforma}', [ProformaController::class, 'destroy'])->name('proformas.destroy');

  Route::get('/exportExcel/{proforma}', [ProformaController::class, 'exportExcel']);
  Route::get('/exportPDF/{id}', [ProformaController::class, 'exportPDF']);
  Route::get('/getImgById/{id}', [ProformaController::class, 'getImgById']);
});
