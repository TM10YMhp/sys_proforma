<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'nombres' => ["required", "string", "max:255"],
      'apellido_primario' => ["required", "string", "max:255"],
      'apellido_secundario' => ["required", "string", "max:255"],
      'ruc' => ["required", "unique:clientes,ruc", "integer:strict", "min:0"],
      'dni' => ["required", "unique:clientes,dni", "integer:strict", "min:0"],
      'telefono' => ["required", "unique:clientes,telefono", "string", "max:255"]
    ];
  }
}
