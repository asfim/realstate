@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i> Post Manual Journal Voucher
        </h4>
        <div class="text-muted small">Double-Entry Posting: Total Debits must strictly equal Total Credits</div>
    </div>
    <div>
        <a href="{{ route('erp.accounting.journals') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Vouchers
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.accounting.journals.store') }}" method="POST" id="journalForm">
            @csrf
            <input type="hidden" name="company_id" value="1">

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Voucher Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Voucher Type <span class="text-danger">*</span></label>
                    <select name="voucher_type" class="form-select" required>
                        <option value="journal">Journal Voucher (JV)</option>
                        <option value="payment">Payment Voucher (PV)</option>
                        <option value="receipt">Receipt Voucher (RV)</option>
                        <option value="contra">Contra Voucher (CV)</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Project Cost Center</label>
                    <select name="project_id" class="form-select">
                        <option value="">-- Corporate / Non-Project --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Reference / Cheque No</label>
                    <input type="text" name="reference" class="form-control" placeholder="e.g. CHQ-994182">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Narration / Particulars <span class="text-danger">*</span></label>
                    <input type="text" name="narration" class="form-control" placeholder="Detailed purpose of transaction..." required>
                </div>
            </div>

            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Double-Entry Lines (Dr = Cr)</h6>

            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle" id="linesTable">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="w-40">GL Account <span class="text-danger">*</span></th>
                            <th>Description</th>
                            <th class="w-20 text-end">Debit (BDT)</th>
                            <th class="w-20 text-end">Credit (BDT)</th>
                            <th class="w-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        {{-- Row 1: Debit Line --}}
                        <tr>
                            <td>
                                <select name="lines[0][account_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Select GL Account --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[0][description]" class="form-control form-control-sm" placeholder="Debit narration"></td>
                            <td><input type="number" step="0.01" name="lines[0][debit]" class="form-control form-control-sm text-end debit-input" value="0.00" oninput="calculateTotals()"></td>
                            <td><input type="number" step="0.01" name="lines[0][credit]" class="form-control form-control-sm text-end credit-input" value="0.00" oninput="calculateTotals()"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-light border text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                        </tr>
                        {{-- Row 2: Credit Line --}}
                        <tr>
                            <td>
                                <select name="lines[1][account_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Select GL Account --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[1][description]" class="form-control form-control-sm" placeholder="Credit narration"></td>
                            <td><input type="number" step="0.01" name="lines[1][debit]" class="form-control form-control-sm text-end debit-input" value="0.00" oninput="calculateTotals()"></td>
                            <td><input type="number" step="0.01" name="lines[1][credit]" class="form-control form-control-sm text-end credit-input" value="0.00" oninput="calculateTotals()"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-light border text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Another Line
                                </button>
                            </td>
                            <td class="text-end text-primary" id="totalDebitDisplay">৳ 0.00</td>
                            <td class="text-end text-success" id="totalCreditDisplay">৳ 0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="balanceAlert" class="alert alert-warning py-2 small d-none">
                <i class="bi bi-exclamation-triangle me-1"></i> Debits and Credits must balance to the exact cent before submitting.
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.accounting.journals') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                    <i class="bi bi-check-circle me-1"></i> Post Journal Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = 2;

function addRow() {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="lines[${rowIndex}][account_id]" class="form-select form-select-sm" required>
                <option value="">-- Select GL Account --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="lines[${rowIndex}][description]" class="form-control form-control-sm"></td>
        <td><input type="number" step="0.01" name="lines[${rowIndex}][debit]" class="form-control form-control-sm text-end debit-input" value="0.00" oninput="calculateTotals()"></td>
        <td><input type="number" step="0.01" name="lines[${rowIndex}][credit]" class="form-control form-control-sm text-end credit-input" value="0.00" oninput="calculateTotals()"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-light border text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    rowIndex++;
}

function removeRow(btn) {
    if (document.querySelectorAll('#linesBody tr').length > 2) {
        btn.closest('tr').remove();
        calculateTotals();
    } else {
        alert('A valid journal entry requires at least 2 lines (1 Debit and 1 Credit).');
    }
}

function calculateTotals() {
    let debits = 0;
    let credits = 0;

    document.querySelectorAll('.debit-input').forEach(input => {
        debits += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.credit-input').forEach(input => {
        credits += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebitDisplay').innerText = '৳ ' + debits.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('totalCreditDisplay').innerText = '৳ ' + credits.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const isBalanced = (Math.abs(debits - credits) < 0.01) && debits > 0;
    const alertBox = document.getElementById('balanceAlert');
    if (!isBalanced) {
        alertBox.classList.remove('d-none');
    } else {
        alertBox.classList.add('d-none');
    }
}
</script>
@endsection

