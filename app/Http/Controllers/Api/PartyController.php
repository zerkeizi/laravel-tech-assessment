<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        return Party::query()
            ->when($request->filled('search'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->string('search')}%")
                    ->orWhere('document', 'like', "%{$request->string('search')}%");
            }))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when(! $request->boolean('with_inactive'), fn ($query) => $query->where('active', true))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));
    }

    public function store(StorePartyRequest $request)
    {
        $party = Party::create($request->validated());

        return response()->json($party, 201);
    }

    public function show(Party $party)
    {
        return $party;
    }

    public function update(UpdatePartyRequest $request, Party $party)
    {
        $party->update($request->validated());

        return $party;
    }

    public function destroy(Party $party)
    {
        $party->deactivate();

        return response()->json(null, 204);
    }
}
