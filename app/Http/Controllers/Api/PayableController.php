<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayableRequest;
use App\Http\Requests\UpdatePayableRequest;
use App\Models\Payable;
use App\Services\FinancialLedgerService;
use Illuminate\Http\Request;

class PayableController extends Controller
{
    public function index(Request $request)
    {
        return Payable::query()
            ->with('party')
            ->when($request->filled('party_id'), fn ($query) => $query->where('party_id', $request->integer('party_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('due_from'), fn ($query) => $query->whereDate('due_date', '>=', $request->date('due_from')))
            ->when($request->filled('due_to'), fn ($query) => $query->whereDate('due_date', '<=', $request->date('due_to')))
            ->orderBy('due_date')
            ->paginate($request->integer('per_page', 15));
    }

    public function store(StorePayableRequest $request)
    {
        $payable = Payable::create($request->validated());

        return response()->json($payable->load('party'), 201);
    }

    public function show(Payable $payable)
    {
        return $payable->load('party');
    }

    public function update(UpdatePayableRequest $request, Payable $payable)
    {
        $payable->update($request->validated());

        return $payable->load('party');
    }

    public function destroy(Payable $payable)
    {
        $payable->delete();

        return response()->json(null, 204);
    }

    public function pay(Payable $payable, FinancialLedgerService $ledger)
    {
        $ledger->recordPayment($payable);

        return $payable->fresh('party');
    }
}
