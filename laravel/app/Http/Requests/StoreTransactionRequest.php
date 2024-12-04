<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTransactionRequest extends FormRequest
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
            'type' => 'required|string|min:1|max:1|in:P,I',
            'brain_coins'=> 'required|numeric|multiple_of:10'
        ];

        match ($this->input('type')) {
            'I'=> $rules = array_merge($rules, [
                    'brain_coins' => 'required|numeric|max:-1',
                    'game_id' => [ 'required', 'integer', Rule::exists('games', 'id')->where('created_user_id', Auth::id()) ]
            ]),
            'P' => $rules = array_merge($rules, [
                    'payment_type' => 'required|string|uppercase|in:MBWAY,PAYPAL,IBAN,MB,VISA',
                    'payment_ref' => 'required|string'
            ]),
            default => null,
        };

        return $rules;
    }
}
