<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMultiplayerGameRequest extends FormRequest
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
        "status" => "required|string|in:E,I",
        "total_turns_winner" => "required_if:status,E|numeric",
        "my_win" => "required_if:status,E|boolean",
        "pairs" => "required_if:status,E|integer"
        ];
    }
}
