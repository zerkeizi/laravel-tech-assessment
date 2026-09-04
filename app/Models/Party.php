<?php

namespace App\Models;

use App\Enums\PartyType;
use App\Exceptions\PartyHasTransactionsException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'name', 'document', 'email', 'phone'])]
class Party extends Model
{
    /** @use HasFactory<\Database\Factories\PartyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => PartyType::class,
        ];
    }

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class);
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Party $party) {
            if ($party->payables()->exists() || $party->receivables()->exists()) {
                throw new PartyHasTransactionsException;
            }
        });
    }
}
