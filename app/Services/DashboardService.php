<?php

namespace App\Services;

use App\Enums\PayableStatus;
use App\Enums\ReceivableStatus;
use App\Models\Payable;
use App\Models\Receivable;

class DashboardService
{
    public function summary(): array
    {
        $totalReceivable = (float) Receivable::pending()->sum('amount');
        $totalReceived = (float) Receivable::where('status', ReceivableStatus::Received)->sum('amount');
        $totalOverdueReceivable = (float) Receivable::where('status', ReceivableStatus::Overdue)->sum('amount');

        $totalPayable = (float) Payable::pending()->sum('amount');
        $totalPaid = (float) Payable::where('status', PayableStatus::Paid)->sum('amount');
        $totalOverduePayable = (float) Payable::where('status', PayableStatus::Overdue)->sum('amount');

        return [
            'total_receivable' => $totalReceivable,
            'total_received' => $totalReceived,
            'total_overdue_receivable' => $totalOverdueReceivable,
            'total_payable' => $totalPayable,
            'total_paid' => $totalPaid,
            'total_overdue_payable' => $totalOverduePayable,
            'projected_balance' => $totalReceivable - $totalPayable,
            'realized_balance' => $totalReceived - $totalPaid,
        ];
    }
}
