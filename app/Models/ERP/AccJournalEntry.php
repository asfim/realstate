<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccJournalEntry extends Model
{
    use HasFactory;

    protected $table = 'acc_journal_entries';

    protected $fillable = [
        'journal_id',
        'account_id',
        'cost_center_id',
        'debit',
        'credit',
        'entry_narration',
    ];

    protected $casts = [
        'debit'  => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(AccJournal::class, 'journal_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AccChartOfAccount::class, 'account_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
}

