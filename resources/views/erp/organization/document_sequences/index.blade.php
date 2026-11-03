@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Document Sequences & Numbering</h4>
        <p class="text-muted small mb-0">Configure auto-incrementing serial prefixes for quotes, invoices, receipts, and vouchers.</p>
    </div>
    <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createSeqModal">
        <i class="bi bi-plus-lg me-1"></i> Add Sequence
    </button>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Document Type</th>
                    <th>Prefix</th>
                    <th>Next Serial</th>
                    <th>Padding</th>
                    <th>Preview Next Number</th>
                    <th>Branch / FY Scope</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sequences as $seq)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark text-capitalize">{{ str_replace('_', ' ', $seq->document_type) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-dark font-monospace px-2 py-1">{{ $seq->prefix }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $seq->next_number }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $seq->padding }} digits</small>
                        </td>
                        <td>
                            <span class="badge bg-primary font-monospace">
                                {{ $seq->prefix }}-{{ date('Y') }}-{{ str_pad((string)$seq->next_number, $seq->padding, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td>
                            <small class="d-block text-muted">Branch: {{ $seq->branch?->name ?? 'All Branches' }}</small>
                            <small class="d-block text-muted">FY: {{ $seq->fiscalYear?->name ?? 'All Years' }}</small>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSeqModal{{ $seq->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Sequence Modal -->
                    <div class="modal fade" id="editSeqModal{{ $seq->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('erp.document-sequences.update', $seq) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Update Sequence: {{ strtoupper($seq->document_type) }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Prefix <span class="text-danger">*</span></label>
                                            <input type="text" name="prefix" class="form-control" value="{{ $seq->prefix }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Next Sequence Number <span class="text-danger">*</span></label>
                                            <input type="number" name="next_number" class="form-control" value="{{ $seq->next_number }}" min="1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Zero-Padding Digits <span class="text-danger">*</span></label>
                                            <input type="number" name="padding" class="form-control" value="{{ $seq->padding }}" min="1" max="10" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-erp-gold">Save Sequence</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No sequences found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sequences->hasPages())
        <div class="p-3 border-top">
            {{ $sequences->links() }}
        </div>
    @endif
</div>

<!-- Create Sequence Modal -->
<div class="modal fade" id="createSeqModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.document-sequences.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Configure Document Sequence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Document Type <span class="text-danger">*</span></label>
                        <select name="document_type" class="form-select" required>
                            <option value="quotation">Quotation (QTE)</option>
                            <option value="booking">Booking (BKG)</option>
                            <option value="contract">Sale Contract (CTR)</option>
                            <option value="invoice">Customer Invoice (INV)</option>
                            <option value="receipt">Money Receipt (REC)</option>
                            <option value="po">Purchase Order (PO)</option>
                            <option value="grn">Goods Receipt Note (GRN)</option>
                            <option value="bill">Vendor Bill (BIL)</option>
                            <option value="payment">Payment Voucher (PAY)</option>
                            <option value="journal">Journal Voucher (JRN)</option>
                            <option value="payroll">Payroll Sheet (PRL)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Prefix Code <span class="text-danger">*</span></label>
                        <input type="text" name="prefix" class="form-control" placeholder="e.g. INV" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Branch (Optional)</label>
                            <select name="branch_id" class="form-select">
                                <option value="">-- All Branches --</option>
                                @foreach($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Fiscal Year (Optional)</label>
                            <select name="fiscal_year_id" class="form-select">
                                <option value="">-- All Fiscal Years --</option>
                                @foreach($fiscalYears as $fy)
                                    <option value="{{ $fy->id }}">{{ $fy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Starting Number <span class="text-danger">*</span></label>
                            <input type="number" name="next_number" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Digit Padding <span class="text-danger">*</span></label>
                            <input type="number" name="padding" class="form-control" value="5" min="1" max="10" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Create Sequence</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

