<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'erp_journals';

    protected $fillable = [
        'company_id',
        'fiscal_period_id',
        'project_id',
        'journal_no',
        'date',
        'voucher_type',
        'reference',
        'narration',
        'total_debit',
        'total_credit',
        'status',
        'posted_at',
        'posted_by',
        'reversed_journal_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'posted_at' => 'datetime',
            'total_debit' => 'decimal:2',
            'total_credit' => 'decimal:2',
        ];
    }

    public function isBalanced(): bool
    {
        return bccomp((string)$this->total_debit, (string)$this->total_credit, 2) === 0;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class, 'fiscal_period_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'posted_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'journal_id');
    }

    public function reversedJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'reversed_journal_id');
    }
}
