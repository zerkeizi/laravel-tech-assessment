<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared by Payable and Receivable. Both models store a `status` column that
 * only ever holds 'pendente' until something explicitly settles or cancels
 * it — "vencido" (overdue) is never written by a controller, it's derived
 * here from due_date + the settlement date column. Any query that needs to
 * know which records are overdue (dashboard, reports) must use scopeOverdue()
 * below rather than filtering on a stored 'vencido' status value, since one
 * is never persisted until the (not-yet-built) daily sync job exists.
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
        return $this->status?->value === 'pendente'
            && $this->due_date !== null
            && $this->due_date->isPast()
            && $this->settlementDate() === null;
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pendente');
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', 'pendente')
            ->where('due_date', '<', now()->toDateString())
            ->whereNull($this->settlementDateColumn());
    }
}
