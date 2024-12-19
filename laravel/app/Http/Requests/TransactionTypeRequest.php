<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionTypeRequest extends FormRequest
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
            'type' => 'string|min:1|max:1|in:P,B,I',
            'page'=> 'numeric|min:1',
            'nickname' => 'string|min:1'
        ];

        if ($this->has('type')) {
            match ($this->input('type')) {
                'I' => $rules = array_merge($rules, [
                        'reward'=> 'boolean',
                    ]),
                'P' => $rules = array_merge($rules, [
                            'euros_lower_limit' => 'numeric|min:1',
                            'euros_higher_limit' => 'numeric|min:' . (int)$this->input('euros_lower_limit') ?? 1,
                        ]),
                default => null,
            };
        }


        return $rules;
    }
}
