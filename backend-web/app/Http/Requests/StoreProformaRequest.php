<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProformaRequest extends FormRequest
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
      'codigo' => ["required", "string", "max:255"],
      'fecha_emision' => ["required", "date"],
      'fecha_vencimiento' => ["required", "date", "after_or_equal:fecha_emision"],
      'subtotal' => ["required", "decimal:0,2", "min:0"],
      'igv_tasa' => ["required", "decimal:0,2", "min:0"],
      'igv_monto' => ["required", "decimal:0,2", "min:0"],
      'total' => ["required", "decimal:0,2", "gt:subtotal"]
    ];
  }
}
