@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-requisitions.index') }}" class="text-decoration-none">Site Requisitions</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Requisition</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Submit Site Material Requisition</h4>
        <p class="text-muted small mb-0">Specify required construction materials, quantities, urgency, and site delivery dates.</p>
    </div>
    <div>
        <a href="{{ route('erp.con-requisitions.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Requisitions
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.con-requisitions.store') }}" method="POST">
        @csrf

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Development Project <span class="text-danger">*</span></label>
                <select name="project_id" class="form-select form-select-sm" required>
                    <option value="">-- Choose Project Site --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->project_code }}) - {{ $p->location }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Required By Date <span class="text-danger">*</span></label>
                <input type="date" name="required_date" class="form-control form-control-sm" value="{{ old('required_date', now()->addDays(3)->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Urgency Level <span class="text-danger">*</span></label>
                <select name="urgency" class="form-select form-select-sm" required>
                    <option value="normal" {{ old('urgency') === 'normal' ? 'selected' : '' }}>Normal (3-5 Days)</option>
                    <option value="urgent" {{ old('urgency') === 'urgent' ? 'selected' : '' }}>Urgent (Within 48h)</option>
                    <option value="emergency" {{ old('urgency') === 'emergency' ? 'selected' : '' }}>Emergency / Critical (Today)</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Site Delivery Location / Spot Details</label>
                <input type="text" name="site_location_notes" class="form-control form-control-sm" placeholder="e.g. Unloading at Basement 2 ramp / Tower A casting deck" value="{{ old('site_location_notes') }}">
            </div>
        </div>

        <!-- Material Items Table -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-check text-erp-gold me-2"></i> Materials Demand List</span>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="addMaterialRow()">
                <i class="bi bi-plus-lg me-1"></i> Add Material
            </button>
        </h6>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle" id="itemsTable">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 45%;">Material Name & Specification <span class="text-danger">*</span></th>
                        <th style="width: 20%;">Unit of Measure <span class="text-danger">*</span></th>
                        <th style="width: 20%;">Requested Quantity <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr>
                        <td>
                            <input type="text" name="items[0][material_name]" class="form-control form-control-sm" placeholder="e.g. 500W TMT Deformed Rod 16mm" required>
                        </td>
                        <td>
                            <select name="items[0][unit_of_measure]" class="form-select form-select-sm" required>
                                <option value="ton">Ton</option>
                                <option value="bag">Bag (50kg)</option>
                                <option value="cft">Cft</option>
                                <option value="sft">Sft</option>
                                <option value="nos">Nos</option>
                                <option value="rft">Rft</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[0][requested_qty]" class="form-control form-control-sm" placeholder="e.g. 25.00" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removeRow(this)" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.con-requisitions.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-send-check me-1"></i> Submit Requisition
            </button>
        </div>
    </form>
</div>

<script>
let rowCount = 1;

function addMaterialRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="text" name="items[${rowCount}][material_name]" class="form-control form-control-sm" placeholder="e.g. Portland Composite Cement (PCC)" required>
        </td>
        <td>
            <select name="items[${rowCount}][unit_of_measure]" class="form-select form-select-sm" required>
                <option value="ton">Ton</option>
                <option value="bag" selected>Bag (50kg)</option>
                <option value="cft">Cft</option>
                <option value="sft">Sft</option>
                <option value="nos">Nos</option>
                <option value="rft">Rft</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="items[${rowCount}][requested_qty]" class="form-control form-control-sm" placeholder="e.g. 500" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    rowCount++;
    updateDeleteButtons();
}

function removeRow(btn) {
    const row = btn.closest('tr');
    if (document.querySelectorAll('#itemsBody tr').length > 1) {
        row.remove();
        updateDeleteButtons();
    }
}

function updateDeleteButtons() {
    const rows = document.querySelectorAll('#itemsBody tr');
    rows.forEach((row) => {
        const btn = row.querySelector('button');
        if (btn) {
            btn.disabled = rows.length === 1;
        }
    });
}
</script>
@endsection

