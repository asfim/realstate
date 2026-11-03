<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConGrn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'con_grns';

    protected $fillable = [
        'company_id',
        'branch_id',
        'project_id',
        'purchase_order_id',
        'vendor_id',
        'grn_code',
        'received_date',
        'challan_no',
        'vehicle_number',
        'received_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(ConPurchaseOrder::class, 'purchase_order_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(ConVendor::class, 'vendor_id');
    }

    public function receivedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'received_by');
    }
}

