@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Record New Income</h4>
        <p class="text-muted small mb-0">Add a revenue transaction, sale commission, booking fee, or rental income</p>
    </div>
    <a href="{{ route('admin.incomes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Incomes
    </a>
</div>

<div class="card p-4 border-0 shadow-sm" style="max-width: 850px;">
    <form action="{{ route('admin.incomes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Title -->
            <div class="col-12 col-md-8">
                <label class="form-label fw-semibold">Income Title / Description <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Booking money for Villa Aurelia Apt 4B" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Amount -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Amount ($) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" value="{{ old('amount') }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Category -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Income Category</label>
                <select name="account_category_id" class="form-select @error('account_category_id') is-invalid @enderror">
                    <option value="">Select Category (Optional)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('account_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('account_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Property Linked (Optional) -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Linked Property (Optional)</label>
                <select name="property_id" class="form-select @error('property_id') is-invalid @enderror">
                    <option value="">None / General Revenue</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ old('property_id') == $prop->id ? 'selected' : '' }}>
                            {{ $prop->title }} ({{ $prop->location }})
                        </option>
                    @endforeach
                </select>
                @error('property_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Transaction Date <span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                @error('transaction_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Wire Transfer</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Bank Cheque</option>
                    <option value="mobile_banking" {{ old('payment_method') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking (bKash/Nagad)</option>
                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit / Debit Card</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reference / Voucher No -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Reference / Receipt No</label>
                <input type="text" name="reference_no" class="form-control @error('reference_no') is-invalid @enderror" placeholder="e.g. REC-2026-081" value="{{ old('reference_no') }}">
                @error('reference_no')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Attachment -->
            <div class="col-12 col-md-12">
                <label class="form-label fw-semibold">Voucher / Receipt Attachment (PDF, JPG, PNG)</label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">Max file size 5MB. Supports receipts, bank slips, and money receipts.</div>
                @error('attachment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Note -->
            <div class="col-12">
                <label class="form-label fw-semibold">Internal Note / Remarks</label>
                <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Any additional payment terms or client details...">{{ old('note') }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit -->
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-gold px-4 py-2 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Save Income Entry
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

