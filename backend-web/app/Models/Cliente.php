<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
  'nombres',
  'apellido_primario',
  'apellido_secundario',
  'ruc',
  'dni',
  'telefono'
)]
class Cliente extends Model
{
  /** @use HasFactory<\Database\Factories\ClienteFactory> */
  use HasFactory;
}
