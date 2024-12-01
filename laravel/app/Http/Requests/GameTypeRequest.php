<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameTypeRequest extends FormRequest
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
            "score_type" => "string|in:time,turns",
            "board" => "string|regex:/^\d+x\d+$/",
            "page" => "numeric|min:1",
            "type"=> "string|min:1|max:1|in:S,M,A",
        ];
    }
}
