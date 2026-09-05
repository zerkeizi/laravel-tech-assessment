<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared by Payable and Receivable. Both models store a `status` column that
 * only ever holds 'pending' until something explicitly settles or cancels
 * it — "overdue" is never written by a controller, it's derived here from
 * due_date + the settlement date column. The App\Jobs\setOverdueRecords.phps job
 * persists 'overdue' onto matching rows once a day (see routes/console.php),
 * but scopeOverdue() below remains the source of truth in between runs —
 * anything that must be accurate right up to the minute (the dashboard)
 * should keep using it rather than filtering on the stored status alone.
 */
trait HasOverdueStatus
{
    /**
     * Column holding the date this record was settled: `payment_date` on
     * Payable, `receipt_date` on Receivable.
     */
    abstract public function settlementDateColumn(): string;

    public function settlementDate(): ?\Illuminate\Support\Carbon
    {
        return $this->{$this->settlementDateColumn()};
    }

    public function isOverdue(): bool
    {
        return $this->status?->value === 'pending'
            && $this->due_date !== null
            && $this->due_date->isPast()
            && $this->settlementDate() === null;
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->whereNull($this->settlementDateColumn());
    }
}