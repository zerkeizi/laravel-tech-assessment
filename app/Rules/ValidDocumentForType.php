<?php

namespace App\Rules;

use App\Enums\PartyType;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDocumentForType implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    # Validação para documentos CPF e CNPJ com base no tipo de pessoa (Individual ou Company)
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = $this->data['type'] ?? null;
        $digits = preg_replace('/\D/', '', (string) $value);

        if ($type === PartyType::Individual->value && strlen($digits) !== 11) {
            $fail('The :attribute must be a valid CPF with 11 digits.');

            return;
        }

        if ($type === PartyType::Company->value && strlen($digits) !== 14) {
            $fail('The :attribute must be a valid CNPJ with 14 digits.');
        }
    }
}
