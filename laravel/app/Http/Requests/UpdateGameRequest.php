<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
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
      $rules = [
        "status" => "required|string|in:PL,E,I",
      ];

      if ($this->input("status") == "E") { 
        array_merge($rules, [
          "total_time" => "required|decimal|min:0",
          "total_turns_winner" => "required|numeric|min:1",
          "winner_user_id" => "numeric|exists:users,id",
        ]);
      }

      return $rules;
    }
}
