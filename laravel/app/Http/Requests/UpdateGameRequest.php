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

      return [
        "status" => "required|string|in:PL,E,I",
        "total_time" => "required_if:status,E|decimal:2,16|min:0",
        "total_turns_winner" => "required_if:status,E|numeric|min:1",
        "winner_user_id" => "nullable|numeric|exists:users,id",
        ];
    }
}
