@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-pos.index') }}" class="text-decoration-none">Purchase Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">Generate PO</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Issue Supplier Purchase Order (PO)</h4>
        <p class="text-muted small mb-0">Contractual procurement order with agreed unit rates, quantity commitments, VAT, and payment terms.</p>
    </div>
    <div>
        <a href="{{ route('erp.con-pos.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to POs
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.con-pos.store') }}" method="POST">
        @csrf

        <!-- Vendor & Project Details -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-building-fill text-erp-gold me-2"></i> Supplier & Project Destination
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Supplier / Vendor <span class="text-danger">*</span></label>
                <select name="vendor_id" class="form-select form-select-sm" required>
                    <option value="">-- Choose Vendor --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->name }} ({{ $v->phone }}) - {{ $v->vendor_code }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Vendor must be approved. <a href="{{ route('erp.con-vendors.create') }}" target="_blank">Add new vendor</a></div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Project Destination <span class="text-danger">*</span></label>
                <select name="project_id" class="form-select form-select-sm" required>
                    <option value="">-- Choose Project Site --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', optional($selectedReq)->project_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->project_code }}) - {{ $p->location }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Linked Requisition (Optional)</label>
                <select name="requisition_id" class="form-select form-select-sm">
                    <option value="">-- Direct Purchase / No Requisition --</option>
                    @foreach($approvedRequisitions as $req)
                        <option value="{{ $req->id }}" {{ (old('requisition_id') == $req->id || ($selectedReq && $selectedReq->id == $req->id)) ? 'selected' : '' }}>
                            {{ $req->requisition_code }} ({{ $req->project->name }}) - {{ count($req->items) }} items
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">PO Issue Date <span class="text-danger">*</span></label>
                <input type="date" name="po_date" class="form-control form-control-sm" value="{{ old('po_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Delivery Due Date</label>
                <input type="date" name="delivery_due_date" class="form-control form-control-sm" value="{{ old('delivery_due_date', now()->addDays(7)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Estimated VAT / Tax (BDT)</label>
                <input type="number" step="0.01" name="tax_vat_amount" id="vatInput" class="form-control form-control-sm" value="{{ old('tax_vat_amount', '0') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Payment Terms</label>
                <input type="text" name="payment_terms" class="form-control form-control-sm" placeholder="e.g. 50% advance, 50% on delivery" value="{{ old('payment_terms', 'Net 30 Days after GRN') }}">
            </div>
        </div>

        <!-- Line Items -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cart-check text-erp-gold me-2"></i> Ordered Materials & Rate Schedule</span>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="addPoRow()">
                <i class="bi bi-plus-lg me-1"></i> Add Line Item
            </button>
        </h6>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle" id="poItemsTable">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 35%;">Item Name & Description <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Unit <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Quantity <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Unit Rate (BDT) <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Line Total</th>
                        <th style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="poItemsBody">
                    @if($selectedReq && $selectedReq->items->isNotEmpty())
                        @foreach($selectedReq->items as $idx => $reqItem)
                            <tr>
                                <td>
                                    <input type="text" name="items[{{ $idx }}][item_name]" class="form-control form-control-sm" value="{{ $reqItem->material_name }}" required>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $idx }}][unit_of_measure]" class="form-control form-control-sm" value="{{ $reqItem->unit_of_measure }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][quantity]" class="form-control form-control-sm qty-input" value="{{ $reqItem->requested_qty }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][unit_rate]" class="form-control form-control-sm rate-input" placeholder="0.00" required>
                                </td>
                                <td class="line-total-cell fw-bold text-dark">৳ 0.00</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removePoRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input type="text" name="items[0][item_name]" class="form-control form-control-sm" placeholder="e.g. 500W TMT Steel Rod 16mm" required>
                            </td>
                            <td>
                                <select name="items[0][unit_of_measure]" class="form-select form-select-sm" required>
                                    <option value="ton">Ton</option>
                                    <option value="bag">Bag</option>
                                    <option value="cft">Cft</option>
                                    <option value="sft">Sft</option>
                                    <option value="nos">Nos</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][quantity]" class="form-control form-control-sm qty-input" placeholder="e.g. 50.00" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][unit_rate]" class="form-control form-control-sm rate-input" placeholder="e.g. 96000" required>
                            </td>
                            <td class="line-total-cell fw-bold text-dark">৳ 0.00</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removePoRow(this)" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Total Calculation Summary Banner -->
        <div class="card bg-light border p-3 mb-4">
            <div class="row text-center">
                <div class="col-md-4 border-end">
                    <div class="text-muted extra-small text-uppercase fw-semibold">Subtotal (Items Total)</div>
                    <div class="fs-5 fw-bold text-dark" id="dispSubtotal">৳ 0.00</div>
                </div>
                <div class="col-md-4 border-end">
                    <div class="text-muted extra-small text-uppercase fw-semibold">Tax / VAT</div>
                    <div class="fs-5 fw-bold text-muted" id="dispVat">৳ 0.00</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted extra-small text-uppercase fw-semibold">Grand Total Commitment</div>
                    <div class="fs-4 fw-bold text-success" id="dispGrandTotal">৳ 0.00</div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Terms & Commercial Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Material test certificate required upon unloading, rejection clause...">{{ old('notes') }}</textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.con-pos.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-send-check me-1"></i> Issue Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
let poRowCount = {{ $selectedReq ? count($selectedReq->items) : 1 }};

function addPoRow() {
    const tbody = document.getElementById('poItemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="text" name="items[${poRowCount}][item_name]" class="form-control form-control-sm" placeholder="e.g. Shah Cement Special" required>
        </td>
        <td>
            <select name="items[${poRowCount}][unit_of_measure]" class="form-select form-select-sm" required>
                <option value="ton">Ton</option>
                <option value="bag" selected>Bag</option>
                <option value="cft">Cft</option>
                <option value="sft">Sft</option>
                <option value="nos">Nos</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="items[${poRowCount}][quantity]" class="form-control form-control-sm qty-input" placeholder="e.g. 500" required>
        </td>
        <td>
            <input type="number" step="0.01" name="items[${poRowCount}][unit_rate]" class="form-control form-control-sm rate-input" placeholder="e.g. 550" required>
        </td>
        <td class="line-total-cell fw-bold text-dark">৳ 0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removePoRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    poRowCount++;
    attachEvents();
    recalculateTotals();
}

function removePoRow(btn) {
    const row = btn.closest('tr');
    if (document.querySelectorAll('#poItemsBody tr').length > 1) {
        row.remove();
        recalculateTotals();
    }
}

function recalculateTotals() {
    let subtotal = 0;
    const rows = document.querySelectorAll('#poItemsBody tr');
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        const rate = parseFloat(row.querySelector('.rate-input')?.value) || 0;
        const line = qty * rate;
        subtotal += line;
        const cell = row.querySelector('.line-total-cell');
        if (cell) cell.innerText = '৳ ' + Number(line.toFixed(2)).toLocaleString();
    });

    const vat = parseFloat(document.getElementById('vatInput')?.value) || 0;
    const grand = subtotal + vat;

    const dispSub = document.getElementById('dispSubtotal');
    const dispV = document.getElementById('dispVat');
    const dispGrand = document.getElementById('dispGrandTotal');

    if (dispSub) dispSub.innerText = '৳ ' + Number(subtotal.toFixed(2)).toLocaleString();
    if (dispV) dispV.innerText = '৳ ' + Number(vat.toFixed(2)).toLocaleString();
    if (dispGrand) dispGrand.innerText = '৳ ' + Number(grand.toFixed(2)).toLocaleString();
}

function attachEvents() {
    document.querySelectorAll('.qty-input, .rate-input').forEach(input => {
        input.removeEventListener('input', recalculateTotals);
        input.addEventListener('input', recalculateTotals);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    attachEvents();
    const vatInput = document.getElementById('vatInput');
    if (vatInput) vatInput.addEventListener('input', recalculateTotals);
    recalculateTotals();
});
</script>
@endsection
