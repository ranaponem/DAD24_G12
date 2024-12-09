<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class StoreUpdateUserRequest extends FormRequest
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
            "name"=> "string|min:3|max:255",
            "email"=> ["email", "min:3","max:255",Rule::unique('users', 'email')->ignore(auth()->user()->id)],
            "nickname"=> ["string","min:4","max:20",Rule::unique('users', 'nickname')->ignore(auth()->user()->id)],
            "photo_image"=> "sometimes|image|mimes:jpeg,jpg,png|max:4096",
        ];
    }
}
