<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PartyHasTransactionsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cannot delete a party that has existing payables or receivables.');
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 409);
    }
}
