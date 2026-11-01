<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitDocument extends Model
{
    use HasFactory;

    protected $table = 'erp_unit_documents';

    protected $fillable = [
        'company_id',
        'unit_id',
        'title',
        'category',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }
}

