<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGameRequest extends FormRequest
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
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'type' => 'required|string|in:S,M',
      'board_id' => 'required|integer|exists:boards,id',
    ];
  }

  public function messages()
  {
    return [
      'type.in' => 'The type must be either "S" (single-player) or "M" (multiplayer).',
    ];

  }
}
