<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_customers';

    protected $fillable = [
        'company_id',
        'customer_code',
        'converted_from_lead_id',
        'name',
        'father_or_husband_name',
        'mother_name',
        'phone',
        'email',
        'emergency_contact',
        'nid_number',
        'tin_number',
        'passport_number',
        'present_address',
        'permanent_address',
        'nominee_name',
        'nominee_relation',
        'nominee_nid',
        'nominee_phone',
        'nominee_photo_path',
        'customer_photo_path',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'converted_from_lead_id');
    }
}

