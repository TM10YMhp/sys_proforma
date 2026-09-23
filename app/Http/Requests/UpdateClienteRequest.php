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
      'apellido_primario' => ["required", "string", "max:255"],
      'apellido_secundario' => ["required", "string", "max:255"],
      'ruc' => [
        "required",
        Rule::unique('clientes', 'ruc')->ignore($clienteId),
        "integer",
        "min:0"
      ],
      'dni' => [
        "required",
        Rule::unique('clientes', 'dni')->ignore($clienteId),
        "integer",
        "min:0"
      ],
      'telefono' => [
        "required",
        Rule::unique('clientes', 'telefono')->ignore($clienteId),
        "string",
        "max:255"
      ]
    ];
  }
}
