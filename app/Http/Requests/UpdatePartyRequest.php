<?php

namespace App\Http\Requests;

use App\Enums\PartyType;
use App\Rules\ValidDocumentForType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PartyType::class)],
            'name' => ['required', 'string', 'max:255'],
            'document' => [
                'required', 'string', 'max:20',
                Rule::unique('parties', 'document')->ignore($this->route('party')),
                new ValidDocumentForType,
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
