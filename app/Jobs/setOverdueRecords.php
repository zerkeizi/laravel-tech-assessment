<?php

namespace App\Jobs;

use App\Enums\PayableStatus;
use App\Enums\ReceivableStatus;
use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Persists the 'overdue' status on payables/receivables whose due_date has
 * passed while still pending. Runs daily so callers can filter/report on
 * `status` directly instead of recomputing scopeOverdue() every time.
 */
class setOverdueRecords implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Payable::overdue()->update(['status' => PayableStatus::Overdue]);
        Receivable::overdue()->update(['status' => ReceivableStatus::Overdue]);
    }
}