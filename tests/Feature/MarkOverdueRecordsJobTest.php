<?php

namespace Tests\Feature;

use App\Enums\PayableStatus;
use App\Enums\ReceivableStatus;
use App\Jobs\setOverdueRecords.phps;
use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class setOverdueRecords.phpsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_pending_payables_past_due_date_as_overdue(): void
    {
        $overduePayable = Payable::factory()->create([
            'status' => PayableStatus::Pending,
            'due_date' => now()->subDay(),
            'payment_date' => null,
        ]);

        $futurePayable = Payable::factory()->create([
            'status' => PayableStatus::Pending,
            'due_date' => now()->addDay(),
            'payment_date' => null,
        ]);

        $paidPayable = Payable::factory()->create([
            'status' => PayableStatus::Paid,
            'due_date' => now()->subDay(),
            'payment_date' => now(),
        ]);

        (new setOverdueRecords.phps)->handle();

        $this->assertSame(PayableStatus::Overdue, $overduePayable->fresh()->status);
        $this->assertSame(PayableStatus::Pending, $futurePayable->fresh()->status);
        $this->assertSame(PayableStatus::Paid, $paidPayable->fresh()->status);
    }

    public function test_it_marks_pending_receivables_past_due_date_as_overdue(): void
    {
        $overdueReceivable = Receivable::factory()->create([
            'status' => ReceivableStatus::Pending,
            'due_date' => now()->subDay(),
            'receipt_date' => null,
        ]);

        $futureReceivable = Receivable::factory()->create([
            'status' => ReceivableStatus::Pending,
            'due_date' => now()->addDay(),
            'receipt_date' => null,
        ]);

        $receivedReceivable = Receivable::factory()->create([
            'status' => ReceivableStatus::Received,
            'due_date' => now()->subDay(),
            'receipt_date' => now(),
        ]);

        (new setOverdueRecords.phps)->handle();

        $this->assertSame(ReceivableStatus::Overdue, $overdueReceivable->fresh()->status);
        $this->assertSame(ReceivableStatus::Pending, $futureReceivable->fresh()->status);
        $this->assertSame(ReceivableStatus::Received, $receivedReceivable->fresh()->status);
    }
}
