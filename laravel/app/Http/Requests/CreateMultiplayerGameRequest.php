<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMultiplayerGameRequest extends FormRequest
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
      'board_id' => 'required|integer|exists:boards,id',
      'player_1_id' => ["required", "integer", Rule::exists('users','id')->whereNot('id', auth()->user()->id)],
    ];
  }

  public function messages()
  {
    return [
      'type.in' => 'The type must be either "S" (single-player) or "M" (multiplayer).',
    ];

  }
}
