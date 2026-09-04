<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceivableRequest;
use App\Http\Requests\UpdateReceivableRequest;
use App\Models\Receivable;
use App\Services\FinancialLedgerService;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        return Receivable::query()
            ->with('party')
            ->when($request->filled('party_id'), fn ($query) => $query->where('party_id', $request->integer('party_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('due_from'), fn ($query) => $query->whereDate('due_date', '>=', $request->date('due_from')))
            ->when($request->filled('due_to'), fn ($query) => $query->whereDate('due_date', '<=', $request->date('due_to')))
            ->orderBy('due_date')
            ->paginate($request->integer('per_page', 15));
    }

    public function store(StoreReceivableRequest $request)
    {
        $receivable = Receivable::create($request->validated());

        return response()->json($receivable->load('party'), 201);
    }

    public function show(Receivable $receivable)
    {
        return $receivable->load('party');
    }

    public function update(UpdateReceivableRequest $request, Receivable $receivable)
    {
        $receivable->update($request->validated());

        return $receivable->load('party');
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();

        return response()->json(null, 204);
    }

    public function receive(Receivable $receivable, FinancialLedgerService $ledger)
    {
        $ledger->recordReceipt($receivable);

        return $receivable->fresh('party');
    }
}
