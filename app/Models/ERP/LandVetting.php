<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandVetting extends Model
{
    use HasFactory;

    protected $table = 'land_vettings';

    protected $fillable = [
        'land_parcel_id',
        'stage_title',
        'status',
        'verified_by_admin_id',
        'lawyer_or_expert_name',
        'findings',
        'attachment_path',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(LandParcel::class, 'land_parcel_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by_admin_id');
    }
}

