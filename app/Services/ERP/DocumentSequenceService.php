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
        'quotation' => 'QTE',
        'booking'   => 'BKG',
        'contract'  => 'CTR',
        'invoice'   => 'INV',
        'receipt'   => 'REC',
        'po'        => 'PO',
        'grn'       => 'GRN',
        'bill'      => 'BIL',
        'payment'   => 'PAY',
        'journal'    => 'JRN',
        'payroll'    => 'PRL',
        'lead'        => 'LED',
        'customer'    => 'CUST',
        'site_visit'  => 'SV',
        'land_parcel' => 'LND',
        'feasibility' => 'FSB',
        'project'     => 'PRJ',
    ];

    /**
     * Atomically get the next sequential document code.
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

            if (!$sequence) {
                // If not found, create a global company sequence
                $prefix = $this->defaultPrefixes[$documentType] ?? strtoupper(substr($documentType, 0, 3));
                $sequence = DocumentSequence::create([
                    'company_id'     => $companyId,
                    'branch_id'      => $branchId,
                    'fiscal_year_id' => $fiscalYearId,
                    'document_type'  => $documentType,
                    'prefix'         => $prefix,
                    'next_number'    => 1,
                    'padding'        => 5,
                ]);
            }

            $currentNumber = $sequence->next_number;
            $sequence->increment('next_number');

            $yearPart = '';
            if ($sequence->fiscal_year_id) {
                $fy = FiscalYear::find($sequence->fiscal_year_id);
                if ($fy) {
                    $yearPart = '-' . date('Y', strtotime($fy->start_date));
                }
            } else {
                $yearPart = '-' . date('Y');
            }

            $formattedNumber = str_pad((string)$currentNumber, $sequence->padding, '0', STR_PAD_LEFT);

            return "{$sequence->prefix}{$yearPart}-{$formattedNumber}";
        });
    }
}

