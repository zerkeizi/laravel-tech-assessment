<?php

namespace App\Http\Requests;

use App\Enums\ReceivableStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceivableRequest extends FormRequest
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
            'party_id' => ['required', 'integer', 'exists:parties,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            // 'received' is only reachable via the dedicated /receive endpoint,
            // and 'overdue' is always derived — never accepted here.
            'status' => ['sometimes', Rule::enum(ReceivableStatus::class)->only([ReceivableStatus::Pending, ReceivableStatus::Cancelled])],
        ];
    }
}
