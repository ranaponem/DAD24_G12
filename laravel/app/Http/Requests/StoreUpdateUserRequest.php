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
            "name"=> "required|string|min:3|max:255",
            "email"=> ["required", "email", "min:3","max:255",Rule::unique('users', 'email')->ignore($this->user?->id)],
            "nickname"=> ["required","string","min:4","max:20",Rule::unique('users', 'nickname')->ignore($this->user?->id)],
            "password"=> ["required","string", Password::min(8)->letters()->numbers()],
            "photo_image"=> "sometimes|image|mimes:jpeg,jpg,png|max:4096",
        ];
    }
}
