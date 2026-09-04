<?php

namespace App\Models;

use App\Enums\PartyType;
use Database\Factories\PartyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'name', 'document', 'email', 'phone', 'active'])]
class Party extends Model
{
    /** @use HasFactory<PartyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => PartyType::class,
            'active' => 'boolean',
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

    public function deactivate(): void
    {
        $this->update(['active' => false]);
    }
}
