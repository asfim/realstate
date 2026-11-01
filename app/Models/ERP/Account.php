<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'erp_accounts';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type', // asset, liability, equity, revenue, expense
        'parent_id',
        'description',
        'opening_balance',
        'current_balance',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function isDebitNormal(): bool
    {
        return in_array($this->type, ['asset', 'expense']);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }

    public function calculateBalance(): float
    {
        $debitTotal = (float) $this->journalLines()->whereHas('journal', function ($q) {
            $q->where('status', 'posted');
        })->sum('debit');

        $creditTotal = (float) $this->journalLines()->whereHas('journal', function ($q) {
            $q->where('status', 'posted');
        })->sum('credit');

        if ($this->isDebitNormal()) {
            return (float)$this->opening_balance + ($debitTotal - $creditTotal);
        } else {
            return (float)$this->opening_balance + ($creditTotal - $debitTotal);
        }
    }
}

