<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccJournal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'acc_journals';

    protected $fillable = [
        'company_id',
        'branch_id',
        'fiscal_year_id',
        'voucher_code',
        'voucher_type',
        'entry_date',
        'total_amount',
        'narration',
        'reference_type',
        'reference_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'entry_date'   => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(AccJournalEntry::class, 'journal_id');
    }
}

