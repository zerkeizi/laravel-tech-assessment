<?php

namespace App\Services;

use App\Enums\PayableStatus;
use App\Enums\ReceivableStatus;
use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Support\Carbon;

class FinancialLedgerService
{
    public function recordPayment(Payable $payable, ?Carbon $paymentDate = null): Payable
    {
        $payable->update([
            'payment_date' => $paymentDate ?? now()->toDateString(),
            'status' => PayableStatus::Paid,
        ]);

        return $payable;
    }

    public function recordReceipt(Receivable $receivable, ?Carbon $receiptDate = null): Receivable
    {
        $receivable->update([
            'receipt_date' => $receiptDate ?? now()->toDateString(),
            'status' => ReceivableStatus::Received,
        ]);

        return $receivable;
    }
}
