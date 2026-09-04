<?php

namespace App\Models;

use App\Enums\PayableStatus;
use App\Models\Concerns\HasOverdueStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['party_id', 'description', 'amount', 'issue_date', 'due_date', 'payment_date', 'status'])]
class Payable extends Model
{
    /** @use HasFactory<\Database\Factories\PayableFactory> */
    use HasFactory, HasOverdueStatus;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'status' => PayableStatus::class,
        ];
    }

    public function settlementDateColumn(): string
    {
        return 'payment_date';
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
