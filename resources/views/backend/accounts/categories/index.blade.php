@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-tag-fill text-warning me-2"></i> Account Categories</h4>
        <p class="text-muted small mb-0">Manage income revenue categories and operational expense types</p>
    </div>
    
    <div class="d-flex gap-2">
        @can('accounts.create')
            <button type="button" class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-circle me-1"></i> Add Category
            </button>
        @endcan
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Accounts Overview
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Income Categories -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold text-success mb-3">
                <i class="bi bi-arrow-down-left-circle-fill me-2"></i> Income Categories
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Usage</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories->where('type', 'income') as $cat)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $cat->name }}</div>
                                    @if(!$cat->is_active)
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ Str::limit($cat->description ?? '&mdash;', 30) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $cat->transactions_count }} entries</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @can('accounts.edit')
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $cat->id }}" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('accounts.delete')
                                            @if($cat->transactions_count === 0)
                                                <form action="{{ route('admin.account-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No income categories defined.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expense Categories -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold text-danger mb-3">
                <i class="bi bi-arrow-up-right-circle-fill me-2"></i> Expense Categories
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Usage</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories->where('type', 'expense') as $cat)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $cat->name }}</div>
                                    @if(!$cat->is_active)
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ Str::limit($cat->description ?? '&mdash;', 30) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $cat->transactions_count }} entries</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @can('accounts.edit')
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $cat->id }}" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('accounts.delete')
                                            @if($cat->transactions_count === 0)
                                                <form action="{{ route('admin.account-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No expense categories defined.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.account-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Account Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="income">Income / Revenue</option>
                            <option value="expense">Expense / Cost</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Legal Fees or Property Commission" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Brief note about what this category covers..."></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="createActive" value="1" checked>
                        <label class="form-check-label fw-semibold" for="createActive">
                            Active category (available for entries)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold btn-sm px-3">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modals -->
@foreach($categories as $cat)
    <div class="modal fade" id="editModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.account-categories.update', $cat->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Category: {{ $cat->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="income" {{ $cat->type === 'income' ? 'selected' : '' }}>Income / Revenue</option>
                                <option value="expense" {{ $cat->type === 'expense' ? 'selected' : '' }}>Expense / Cost</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="2" class="form-control">{{ $cat->description }}</textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editActive{{ $cat->id }}" value="1" {{ $cat->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="editActive{{ $cat->id }}">
                                Active category
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gold btn-sm px-3">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

