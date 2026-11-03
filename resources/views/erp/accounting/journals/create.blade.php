@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.acc-journals.index') }}" class="text-decoration-none">Journal Vouchers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Post Voucher</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Post Double-Entry Journal Voucher</h4>
        <p class="text-muted small mb-0">Record General Ledger transactions with strict balanced Debit = Credit mathematical verification.</p>
    </div>
    <div>
        <a href="{{ route('erp.acc-journals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Journals
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom p-4">
    <form action="{{ route('erp.acc-journals.store') }}" method="POST" id="journalForm">
        @csrf

        <!-- Voucher Header -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-file-earmark-ruled text-erp-gold me-2"></i> Voucher Header & Fiscal Period
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Voucher Type <span class="text-danger">*</span></label>
                <select name="voucher_type" class="form-select form-select-sm" required>
                    <option value="JV" {{ old('voucher_type') === 'JV' ? 'selected' : '' }}>JV - General Journal Voucher</option>
                    <option value="BPV" {{ old('voucher_type') === 'BPV' ? 'selected' : '' }}>BPV - Bank Payment Voucher</option>
                    <option value="BRV" {{ old('voucher_type') === 'BRV' ? 'selected' : '' }}>BRV - Bank Receipt Voucher</option>
                    <option value="CPV" {{ old('voucher_type') === 'CPV' ? 'selected' : '' }}>CPV - Cash Payment Voucher</option>
                    <option value="CRV" {{ old('voucher_type') === 'CRV' ? 'selected' : '' }}>CRV - Cash Receipt Voucher</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Voucher Date <span class="text-danger">*</span></label>
                <input type="date" name="entry_date" class="form-control form-control-sm" value="{{ old('entry_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Master Narration / Transaction Purpose <span class="text-danger">*</span></label>
                <input type="text" name="narration" class="form-control form-control-sm" placeholder="e.g. Supplier payment to BSRM for Banani site rod procurement" value="{{ old('narration') }}" required>
            </div>
        </div>

        <!-- Double-Entry Lines Table -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table text-erp-gold me-2"></i> General Ledger Entries (Debits & Credits)</span>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="addJournalLine()">
                <i class="bi bi-plus-lg me-1"></i> Add Entry Line
            </button>
        </h6>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle" id="linesTable">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 35%;">GL Account Head <span class="text-danger">*</span></th>
                        <th style="width: 25%;">Cost Centre (Optional)</th>
                        <th style="width: 17%;">Debit Amount (BDT)</th>
                        <th style="width: 17%;">Credit Amount (BDT)</th>
                        <th style="width: 6%;"></th>
                    </tr>
                </thead>
                <tbody id="linesBody">
                    <!-- Line 1: Debit Default -->
                    <tr>
                        <td>
                            <select name="lines[0][account_id]" class="form-select form-select-sm" required>
                                <option value="">-- Choose Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->account_code }} - {{ $acc->account_name }} ({{ ucfirst($acc->account_type) }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="lines[0][cost_center_id]" class="form-select form-select-sm">
                                <option value="">-- No Cost Centre --</option>
                                @foreach($costCenters as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="lines[0][debit]" class="form-control form-control-sm debit-input" placeholder="0.00" value="0">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="lines[0][credit]" class="form-control form-control-sm credit-input" placeholder="0.00" value="0">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removeLine(this)" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Line 2: Credit Default -->
                    <tr>
                        <td>
                            <select name="lines[1][account_id]" class="form-select form-select-sm" required>
                                <option value="">-- Choose Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->account_code }} - {{ $acc->account_name }} ({{ ucfirst($acc->account_type) }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="lines[1][cost_center_id]" class="form-select form-select-sm">
                                <option value="">-- No Cost Centre --</option>
                                @foreach($costCenters as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="lines[1][debit]" class="form-control form-control-sm debit-input" placeholder="0.00" value="0">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="lines[1][credit]" class="form-control form-control-sm credit-input" placeholder="0.00" value="0">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removeLine(this)" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Calculated Totals:</td>
                        <td class="fw-bold text-dark fs-6" id="totalDebitCell">৳ 0.00</td>
                        <td class="fw-bold text-dark fs-6" id="totalCreditCell">৳ 0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Balance Status Bar -->
        <div id="balanceAlert" class="alert alert-warning d-flex align-items-center justify-content-between p-3 mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5" id="balanceIcon"></i>
                <div>
                    <strong id="balanceStatusText">Voucher Not Balanced!</strong>
                    <div class="small" id="balanceSubText">Total Debits must exactly equal Total Credits before posting.</div>
                </div>
            </div>
            <div class="fs-5 fw-bold" id="balanceDifferenceText">Diff: ৳ 0.00</div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.acc-journals.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold" id="submitBtn" disabled>
                <i class="bi bi-check-circle me-1"></i> Post to General Ledger
            </button>
        </div>
    </form>
</div>

<script>
let lineCount = 2;

const accountsOptions = `@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }} ({{ ucfirst($acc->account_type) }})</option>@endforeach`;
const costCenterOptions = `@foreach($costCenters as $cc)<option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>@endforeach`;

function addJournalLine() {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="lines[${lineCount}][account_id]" class="form-select form-select-sm" required>
                <option value="">-- Choose Account --</option>
                ${accountsOptions}
            </select>
        </td>
        <td>
            <select name="lines[${lineCount}][cost_center_id]" class="form-select form-select-sm">
                <option value="">-- No Cost Centre --</option>
                ${costCenterOptions}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="lines[${lineCount}][debit]" class="form-control form-control-sm debit-input" placeholder="0.00" value="0">
        </td>
        <td>
            <input type="number" step="0.01" name="lines[${lineCount}][credit]" class="form-control form-control-sm credit-input" placeholder="0.00" value="0">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removeLine(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    lineCount++;
    attachEvents();
    recalculateJournal();
}

function removeLine(btn) {
    const row = btn.closest('tr');
    if (document.querySelectorAll('#linesBody tr').length > 2) {
        row.remove();
        recalculateJournal();
    }
}

function recalculateJournal() {
    let sumDr = 0;
    let sumCr = 0;

    document.querySelectorAll('.debit-input').forEach(input => {
        sumDr += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.credit-input').forEach(input => {
        sumCr += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebitCell').innerText = '৳ ' + Number(sumDr.toFixed(2)).toLocaleString();
    document.getElementById('totalCreditCell').innerText = '৳ ' + Number(sumCr.toFixed(2)).toLocaleString();

    const diff = Math.abs(sumDr - sumCr);
    const alertBox = document.getElementById('balanceAlert');
    const statusText = document.getElementById('balanceStatusText');
    const subText = document.getElementById('balanceSubText');
    const diffText = document.getElementById('balanceDifferenceText');
    const icon = document.getElementById('balanceIcon');
    const submitBtn = document.getElementById('submitBtn');

    if (diff < 0.01 && sumDr > 0) {
        alertBox.className = 'alert alert-success d-flex align-items-center justify-content-between p-3 mb-4';
        statusText.innerText = 'Voucher Perfectly Balanced!';
        subText.innerText = 'Debits equal Credits. Ready for ledger posting.';
        diffText.innerText = 'Balanced: ৳ ' + Number(sumDr.toFixed(2)).toLocaleString();
        icon.className = 'bi bi-check-circle-fill fs-5 text-success';
        submitBtn.disabled = false;
    } else {
        alertBox.className = 'alert alert-warning d-flex align-items-center justify-content-between p-3 mb-4';
        statusText.innerText = 'Voucher Out of Balance!';
        subText.innerText = 'Total Debits must exactly equal Total Credits before posting.';
        diffText.innerText = 'Difference: ৳ ' + Number(diff.toFixed(2)).toLocaleString();
        icon.className = 'bi bi-exclamation-triangle-fill fs-5 text-warning';
        submitBtn.disabled = true;
    }

    const rows = document.querySelectorAll('#linesBody tr');
    rows.forEach(r => {
        const btn = r.querySelector('button');
        if (btn) btn.disabled = rows.length <= 2;
    });
}

function attachEvents() {
    document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
        input.removeEventListener('input', recalculateJournal);
        input.addEventListener('input', recalculateJournal);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    attachEvents();
    recalculateJournal();
});
</script>
@endsection
