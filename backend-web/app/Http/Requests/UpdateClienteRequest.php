<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
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
    $clienteId = $this->route('cliente');

    return [
      'nombres' => ["required", "string", "max:255"],
      'apellido_primario' => ["nullable", "string", "max:255"],
      'apellido_secundario' => ["nullable", "string", "max:255"],
      'ruc' => [
        "nullable",
        Rule::unique('clientes', 'ruc')->ignore($clienteId),
        "integer",
        "min:0"
      ],
      'dni' => [
        "nullable",
        Rule::unique('clientes', 'dni')->ignore($clienteId),
        "integer",
        "min:0"
      ],
      'telefono' => [
        "nullable",
        Rule::unique('clientes', 'telefono')->ignore($clienteId),
        "string",
        "max:255"
      ]
    ];
  }
}
