<?php

namespace App\Services;

use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportService
{
    public function generate(Request $request): array
    {
        $type = $request->string('type', 'both')->value();
        $partyId = $request->integer('party_id') ?: null;
        $status = $request->string('status')->value() ?: null;
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);

        $items = collect();

        if ($type === 'payable' || $type === 'both') {
            $items = $items->merge(
                $this->filteredQuery(Payable::query(), $partyId, $status, $dateFrom, $dateTo)
                    ->get()
                    ->map(fn (Payable $payable) => $this->toItem($payable, 'payable'))
            );
        }

        if ($type === 'receivable' || $type === 'both') {
            $items = $items->merge(
                $this->filteredQuery(Receivable::query(), $partyId, $status, $dateFrom, $dateTo)
                    ->get()
                    ->map(fn (Receivable $receivable) => $this->toItem($receivable, 'receivable'))
            );
        }

        $items = $items->sortBy('due_date')->values();

        $totals = [
            'count' => $items->count(),
            'total_amount' => (float) $items->sum('amount'),
            'by_status' => $items->groupBy('status')->map(fn ($group) => (float) $group->sum('amount')),
        ];

        $paged = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
        );

        return [
            'filters' => [
                'type' => $type,
                'party_id' => $partyId,
                'status' => $status,
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
            ],
            'items' => $paged,
            'totals' => $totals,
        ];
    }

    protected function filteredQuery($query, ?int $partyId, ?string $status, $dateFrom, $dateTo)
    {
        return $query->with('party')
            ->when($partyId, fn ($query) => $query->where('party_id', $partyId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($dateFrom, fn ($query) => $query->whereDate('due_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('due_date', '<=', $dateTo));
    }

    protected function toItem(Payable|Receivable $model, string $kind): array
    {
        return [
            'kind' => $kind,
            'id' => $model->id,
            'party' => $model->party,
            'description' => $model->description,
            'amount' => (float) $model->amount,
            'issue_date' => $model->issue_date->toDateString(),
            'due_date' => $model->due_date->toDateString(),
            'settlement_date' => $model->settlementDate()?->toDateString(),
            'status' => $model->status->value,
        ];
    }
}
