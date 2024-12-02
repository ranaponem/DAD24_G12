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
      'created_user_id' => 'required|integer|exists:users,id',
      'winner_user_id' => 'nullable|integer|exists:users,id',
      'began_at' => 'nullable|date',
      'ended_at' => 'nullable|date|after_or_equal:began_at',
      'total_time' => 'nullable|numeric|min:0',
      'board_id' => 'required|integer|exists:boards,id',
    ];
  }

  public function messages()
  {
    return [
      'type.in' => 'The type must be either "S" (single-player) or "M" (multiplayer).',
      'status.in' => 'The status must be one of: "PE", "PL", "E", or "I".',
    ];

  }
}
