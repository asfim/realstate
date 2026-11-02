@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Edit Expense Record</h4>
        <p class="text-muted small mb-0">Modify expense record for #{{ $expense->id }}</p>
    </div>
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Expenses
    </a>
</div>

<div class="card p-4 border-0 shadow-sm" style="max-width: 850px;">
    <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Title -->
            <div class="col-12 col-md-8">
                <label class="form-label fw-semibold">Expense Title / Description <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $expense->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Amount -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Amount ($) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Category -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Expense Category</label>
                <select name="account_category_id" class="form-select @error('account_category_id') is-invalid @enderror">
                    <option value="">Select Category (Optional)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('account_category_id', $expense->account_category_id) == $cat->id ? 'selected' : '' }}>
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
                    <option value="">None / General Corporate Expense</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ old('property_id', $expense->property_id) == $prop->id ? 'selected' : '' }}>
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
                <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', $expense->transaction_date->format('Y-m-d')) }}" required>
                @error('transaction_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Wire Transfer</option>
                    <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="cheque" {{ old('payment_method', $expense->payment_method) == 'cheque' ? 'selected' : '' }}>Bank Cheque</option>
                    <option value="mobile_banking" {{ old('payment_method', $expense->payment_method) == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking (bKash/Nagad)</option>
                    <option value="card" {{ old('payment_method', $expense->payment_method) == 'card' ? 'selected' : '' }}>Credit / Debit Card</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reference / Voucher No -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Voucher / Bill No</label>
                <input type="text" name="reference_no" class="form-control @error('reference_no') is-invalid @enderror" value="{{ old('reference_no', $expense->reference_no) }}">
                @error('reference_no')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Attachment -->
            <div class="col-12 col-md-12">
                <label class="form-label fw-semibold">Bill / Receipt Attachment</label>
                @if($expense->attachment)
                    <div class="mb-2 small">
                        Current attachment: <a href="{{ asset_url($expense->attachment) }}" target="_blank" class="fw-semibold text-primary"><i class="bi bi-file-earmark-arrow-down me-1"></i> View File</a>
                    </div>
                @endif
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">Upload new file to replace existing attachment.</div>
                @error('attachment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Note -->
            <div class="col-12">
                <label class="form-label fw-semibold">Internal Note / Remarks</label>
                <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ old('note', $expense->note) }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit -->
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Update Expense Record
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

