@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Construction Bill of Quantities (BOQ)</h4>
        <p class="text-muted small mb-0">Project engineering estimates, material rates, structural budgets, and actual consumption tracking.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addBoqModal">
            <i class="bi bi-plus-circle me-1"></i> Add BOQ Line Item
        </button>
    </div>
</div>

<!-- Project Selector -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.con-boq.index') }}" class="row g-2 align-items-center">
        <div class="col-md-8">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white fw-semibold"><i class="bi bi-building me-1"></i> Active Project:</span>
                <select name="project_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ ($selectedProject && $selectedProject->id == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->project_code }}) - {{ $p->location }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <button type="submit" class="btn btn-dark btn-sm">Refresh BOQ</button>
        </div>
    </form>
</div>

@if(!$selectedProject)
    <div class="card card-custom p-5 text-center text-muted">
        <i class="bi bi-clipboard2-data fs-1 d-block mb-2 text-secondary opacity-50"></i>
        Please register or select a development project to view and manage Bill of Quantities.
    </div>
@else
    @php
        $variance = $totalEstimatedBudget - $totalActualCost;
    @endphp
    <!-- Overview Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-primary h-100">
                <div class="text-muted extra-small text-uppercase fw-semibold">Estimated Budget (BOQ)</div>
                <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($totalEstimatedBudget, 2) }}</div>
                <div class="text-muted extra-small mt-1">{{ count($boqItems) }} line items mapped</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-warning h-100">
                <div class="text-muted extra-small text-uppercase fw-semibold">Actual Material Consumed</div>
                <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($totalActualCost, 2) }}</div>
                <div class="text-muted extra-small mt-1">
                    @if($totalEstimatedBudget > 0)
                        {{ round(($totalActualCost / $totalEstimatedBudget) * 100, 1) }}% budget utilized
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 border-start border-4 {{ $variance >= 0 ? 'border-success' : 'border-danger' }} h-100">
                <div class="text-muted extra-small text-uppercase fw-semibold">Budget Remaining / Variance</div>
                <div class="fs-4 fw-bold {{ $variance >= 0 ? 'text-success' : 'text-danger' }} mt-1">৳ {{ number_format($variance, 2) }}</div>
                <div class="text-muted extra-small mt-1">{{ $variance >= 0 ? 'Within Budget' : 'Cost Overrun Alert' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-info h-100">
                <div class="text-muted extra-small text-uppercase fw-semibold">Construction Scope</div>
                <div class="fs-5 fw-bold text-dark mt-1">{{ $selectedProject->total_floors }} Floors</div>
                <div class="text-muted extra-small mt-1">{{ $selectedProject->total_units }} Units • {{ number_format($selectedProject->total_construction_sqft ?? 0) }} sft GFA</div>
            </div>
        </div>
    </div>

    <!-- BOQ Items Table -->
    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Category</th>
                        <th>Item Description</th>
                        <th>Unit</th>
                        <th>Estimated Qty</th>
                        <th>Est. Rate (BDT)</th>
                        <th>Total Est. Cost</th>
                        <th>Consumed Qty</th>
                        <th>Actual Cost</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boqItems as $item)
                        @php
                            $catBadges = [
                                'substructure' => 'bg-secondary',
                                'superstructure' => 'bg-primary',
                                'finishing' => 'bg-info text-dark',
                                'mep' => 'bg-warning text-dark',
                                'external' => 'bg-dark',
                            ];
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <span class="badge {{ $catBadges[$item->category] ?? 'bg-secondary' }}">
                                    {{ ucfirst($item->category) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->item_name }}</div>
                                @if($item->specifications)
                                    <div class="text-muted extra-small">{{ Str::limit($item->specifications, 60) }}</div>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ strtoupper($item->unit_of_measure) }}</span></td>
                            <td class="fw-semibold">{{ number_format($item->estimated_quantity, 2) }}</td>
                            <td>৳ {{ number_format($item->estimated_unit_rate, 2) }}</td>
                            <td class="fw-bold text-dark">৳ {{ number_format($item->total_estimated_cost, 2) }}</td>
                            <td class="fw-semibold {{ $item->actual_consumed_qty > $item->estimated_quantity ? 'text-danger' : 'text-dark' }}">
                                {{ number_format($item->actual_consumed_qty, 2) }}
                            </td>
                            <td class="fw-bold text-dark">৳ {{ number_format($item->actual_total_cost, 2) }}</td>
                            <td class="text-end pe-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1"
                                        onclick="openConsumeModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $item->actual_consumed_qty }}, {{ $item->actual_total_cost }})">
                                    <i class="bi bi-pencil-square"></i> Log Consumed
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-spreadsheet fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No BOQ line items found for {{ $selectedProject->name }}.
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-erp-gold" data-bs-toggle="modal" data-bs-target="#addBoqModal">Add First BOQ Item</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Add BOQ Item Modal -->
@if($selectedProject)
<div class="modal fade" id="addBoqModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('erp.con-boq.store') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add BOQ Line Item - {{ $selectedProject->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phase / Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select form-select-sm" required>
                            <option value="substructure">Substructure (Piling, Mat Foundation, Basement)</option>
                            <option value="superstructure">Superstructure (Columns, Beams, Slabs, RCC)</option>
                            <option value="finishing">Finishing (Plaster, Tiles, Paint, Doors, Windows)</option>
                            <option value="mep">MEP (Electrical Wiring, Substation, Plumbing, Pumps)</option>
                            <option value="external">External Works (Boundary Wall, Landscaping, Gate)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control form-control-sm" placeholder="e.g. 500W Deformed TMT Steel Rod 16mm/20mm" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Unit of Measure <span class="text-danger">*</span></label>
                            <select name="unit_of_measure" class="form-select form-select-sm" required>
                                <option value="ton">Ton (Metric Ton)</option>
                                <option value="bag">Bag (50 Kg Cement)</option>
                                <option value="cft">Cft (Cubic Feet - Sand/Stone)</option>
                                <option value="sft">Sft (Square Feet - Tiles/Glass)</option>
                                <option value="nos">Nos (Pieces/Bricks)</option>
                                <option value="rft">Rft (Running Feet)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Estimated Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="estimated_quantity" class="form-control form-control-sm" placeholder="e.g. 150.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estimated Unit Rate (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="estimated_unit_rate" class="form-control form-control-sm" placeholder="e.g. 98000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Material Grade / Specifications</label>
                        <textarea name="specifications" class="form-control form-control-sm" rows="2" placeholder="e.g. ASTM A615 Grade 60 / Shah Special 42.5N"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold px-4">Add to BOQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Consumed Modal -->
<div class="modal fade" id="consumeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="consumeForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="consumeTitle">Update Consumed Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Actual Consumed Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="actual_consumed_qty" id="consumeQty" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Actual Incurred Cost (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="actual_total_cost" id="consumeCost" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold px-4">Save Consumption</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openConsumeModal(itemId, itemName, currentQty, currentCost) {
    const modal = new bootstrap.Modal(document.getElementById('consumeModal'));
    const form = document.getElementById('consumeForm');
    const title = document.getElementById('consumeTitle');
    const qtyInput = document.getElementById('consumeQty');
    const costInput = document.getElementById('consumeCost');

    form.action = `/erp/construction/boq/${itemId}/consumption`;
    title.innerText = `Update Consumption: ${itemName}`;
    qtyInput.value = currentQty;
    costInput.value = currentCost;

    modal.show();
}
</script>
@endif
@endsection
