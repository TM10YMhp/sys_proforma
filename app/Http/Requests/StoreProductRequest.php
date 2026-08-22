<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
    // https://laravel.com/framework/docs/validation#available-validation-rules
    return [
      'nombre' => ["required", "string", "max:255"],
      'descripcion' => ["required", "string", "max:255"],
      'stock' => ["required", "integer:strict", "min:0"],
      'precio' => ["required", "decimal:0,2", "min:0"],
      'unidad_medida' => ["required", "string", "max:255"],
      'activo' => ["required", "boolean:strict", "max:255"],
    ];
  }
}
