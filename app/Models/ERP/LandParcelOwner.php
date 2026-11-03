<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandParcelOwner extends Model
{
    use HasFactory;

    protected $table = 'land_parcel_owners';

    protected $fillable = [
        'land_parcel_id',
        'name',
        'share_percentage',
        'phone',
        'nid_number',
        'signing_money_share',
        'is_mou_signed',
        'mou_deed_no',
        'notes',
    ];

    protected $casts = [
        'share_percentage'    => 'decimal:2',
        'signing_money_share' => 'decimal:2',
        'is_mou_signed'       => 'boolean',
    ];

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(LandParcel::class, 'land_parcel_id');
    }
}
