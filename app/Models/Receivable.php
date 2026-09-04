<?php

namespace App\Models;

use App\Enums\ReceivableStatus;
use App\Models\Concerns\HasOverdueStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['party_id', 'description', 'amount', 'issue_date', 'due_date', 'receipt_date', 'status'])]
class Receivable extends Model
{
    /** @use HasFactory<\Database\Factories\ReceivableFactory> */
    use HasFactory, HasOverdueStatus;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'receipt_date' => 'date',
            'status' => ReceivableStatus::class,
        ];
    }

    public function settlementDateColumn(): string
    {
        return 'receipt_date';
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
