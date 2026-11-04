<?php

namespace App\Services\ERP;

use App\Models\ERP\DocumentSequence;
use App\Models\ERP\FiscalYear;
use Illuminate\Support\Facades\DB;

class DocumentSequenceService
{
    /**
     * Default prefixes mapped by document type.
     */
    protected array $defaultPrefixes = [
        'quotation'      => 'QTE',
        'booking'        => 'BKG',
        'contract'       => 'CTR',
        'invoice'        => 'INV',
        'receipt'        => 'REC',
        'po'             => 'PO',
        'grn'            => 'GRN',
        'bill'           => 'BIL',
        'payment'        => 'PAY',
        'journal'        => 'JRN',
        'payroll'        => 'PRL',
        'lead'           => 'LED',
        'customer'       => 'CUST',
        'site_visit'     => 'SV',
        'land_parcel'    => 'LND',
        'feasibility'    => 'FSB',
        'project'        => 'PRJ',
        'agency_listing' => 'LST',
        'agency_deal'    => 'DEA',
        'tenant_lease'   => 'LSE',
        'vendor'         => 'VEN',
        'requisition'    => 'REQ',
        'employee'       => 'EMP',
        'laborer'        => 'LAB',
    ];

    /**
     * Document types mapped to Model and unique code column for collision detection.
     */
    protected array $modelMappings = [
        'booking'        => [\App\Models\ERP\DevSalesBooking::class, 'booking_code'],
        'po'             => [\App\Models\ERP\ConPurchaseOrder::class, 'po_code'],
        'grn'            => [\App\Models\ERP\ConGoodsReceivedNote::class, 'grn_code'],
        'journal'        => [\App\Models\ERP\AccJournal::class, 'voucher_code'],
        'payroll'        => [\App\Models\ERP\HrPayroll::class, 'payroll_code'],
        'lead'           => [\App\Models\ERP\CrmLead::class, 'lead_code'],
        'customer'       => [\App\Models\ERP\CrmCustomer::class, 'customer_code'],
        'land_parcel'    => [\App\Models\ERP\LandParcel::class, 'parcel_code'],
        'feasibility'    => [\App\Models\ERP\LandDevelopmentFeasibility::class, 'feasibility_code'],
        'project'        => [\App\Models\ERP\DevProject::class, 'project_code'],
        'agency_listing' => [\App\Models\ERP\AgencyListing::class, 'listing_code'],
        'agency_deal'    => [\App\Models\ERP\AgencyDeal::class, 'deal_code'],
        'tenant_lease'   => [\App\Models\ERP\AgencyTenantLease::class, 'lease_code'],
        'vendor'         => [\App\Models\ERP\ConVendor::class, 'vendor_code'],
        'requisition'    => [\App\Models\ERP\ConMaterialRequisition::class, 'requisition_code'],
        'employee'       => [\App\Models\ERP\HrEmployee::class, 'employee_code'],
        'laborer'        => [\App\Models\ERP\HrDailyLaborer::class, 'laborer_code'],
    ];

    /**
     * Atomically get the next sequential document code with collision avoidance.
     */
    public function getNextNumber(
        string $documentType,
        int $companyId,
        ?int $branchId = null,
        ?int $fiscalYearId = null
    ): string {
        return DB::transaction(function () use ($documentType, $companyId, $branchId, $fiscalYearId) {
            // Find existing sequence with pessimistic lock
            $sequence = DocumentSequence::where('company_id', $companyId)
                ->where('document_type', $documentType)
                ->where(function ($q) use ($branchId) {
                    if ($branchId) {
                        $q->where('branch_id', $branchId)->orWhereNull('branch_id');
                    } else {
                        $q->whereNull('branch_id');
                    }
                })
                ->where(function ($q) use ($fiscalYearId) {
                    if ($fiscalYearId) {
                        $q->where('fiscal_year_id', $fiscalYearId)->orWhereNull('fiscal_year_id');
                    } else {
                        $q->whereNull('fiscal_year_id');
                    }
                })
                ->orderByRaw('branch_id IS NOT NULL DESC, fiscal_year_id IS NOT NULL DESC')
                ->lockForUpdate()
                ->first();

            $yearPart = '';
            $effectiveFyId = $sequence?->fiscal_year_id ?? $fiscalYearId;
            if ($effectiveFyId) {
                $fy = FiscalYear::find($effectiveFyId);
                if ($fy) {
                    $yearPart = '-' . date('Y', strtotime($fy->start_date));
                }
            } else {
                $yearPart = '-' . date('Y');
            }

            if (!$sequence) {
                // If not found, create a global company sequence with intelligent initial next_number
                $prefix = $this->defaultPrefixes[$documentType] ?? strtoupper(substr($documentType, 0, 3));
                $initialNext = $this->getInitialNextNumber($documentType, $prefix, $yearPart);

                $sequence = DocumentSequence::create([
                    'company_id'     => $companyId,
                    'branch_id'      => $branchId,
                    'fiscal_year_id' => $fiscalYearId,
                    'document_type'  => $documentType,
                    'prefix'         => $prefix,
                    'next_number'    => $initialNext,
                    'padding'        => 5,
                ]);
            }

            // Loop to ensure candidate code is 100% unique and does not collide with any existing record
            do {
                $currentNumber = $sequence->next_number;
                $sequence->next_number = $currentNumber + 1;
                $sequence->save();

                $formattedNumber = str_pad((string)$currentNumber, $sequence->padding, '0', STR_PAD_LEFT);
                $candidateCode = "{$sequence->prefix}{$yearPart}-{$formattedNumber}";

                $exists = false;
                if (isset($this->modelMappings[$documentType])) {
                    [$modelClass, $column] = $this->modelMappings[$documentType];
                    if ($modelClass && class_exists($modelClass)) {
                        $query = $modelClass::where($column, $candidateCode);
                        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass))) {
                            $query = $modelClass::withTrashed()->where($column, $candidateCode);
                        }
                        $exists = $query->exists();
                    }
                }
            } while ($exists);

            return $candidateCode;
        });
    }

    /**
     * Determine initial next_number by checking existing records in the table.
     */
    protected function getInitialNextNumber(string $documentType, string $prefix, string $yearPart): int
    {
        if (!isset($this->modelMappings[$documentType])) {
            return 1;
        }

        [$modelClass, $column] = $this->modelMappings[$documentType];
        if (!$modelClass || !class_exists($modelClass)) {
            return 1;
        }

        $expectedPrefix = "{$prefix}{$yearPart}-";
        $prefixLen = strlen($expectedPrefix);

        $query = $modelClass::query();
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass))) {
            $query = $modelClass::withTrashed();
        }

        $codes = $query->where($column, 'like', "{$expectedPrefix}%")->pluck($column)->all();
        if (empty($codes)) {
            return 1;
        }

        $maxNum = 0;
        foreach ($codes as $code) {
            $numPart = substr($code, $prefixLen);
            if (is_numeric($numPart)) {
                $maxNum = max($maxNum, (int)$numPart);
            }
        }

        return $maxNum + 1;
    }
}
