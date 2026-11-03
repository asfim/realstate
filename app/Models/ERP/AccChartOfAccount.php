<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'acc_chart_of_accounts';

    protected $fillable = [
        'company_id',
        'parent_id',
        'account_code',
        'account_name',
        'account_type',
        'normal_balance',
        'is_reconciled',
        'is_system_locked',
        'status',
        'description',
    ];

    protected $casts = [
        'is_reconciled'    => 'boolean',
        'is_system_locked' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(AccJournalEntry::class, 'account_id');
    }
}

