@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Customer Submissions / Enquiries</h4>
        <p class="text-muted small mb-0">Manage customer form submissions and private tour requests</p>
    </div>
</div>

<div class="card p-4">
    <!-- Filter and Search -->
    <form action="{{ route('admin.submissions.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>New / Unread</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
            </select>
        </div>
        <div class="col-md-4 text-end">
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
            @endif
            <button type="submit" class="btn btn-gold">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Status</th>
                    <th>Customer Name</th>
                    <th>Contact Info</th>
                    <th>Project / Property</th>
                    <th>Submitted Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr class="{{ !$sub->is_read ? 'table-warning fw-bold' : '' }}">
                    <td>
                        @if(!$sub->is_read)
                            <span class="badge bg-danger"><i class="bi bi-bell-fill me-1"></i> New</span>
                        @else
                            <span class="badge bg-secondary">Read</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold">{{ $sub->name }}</div>
                    </td>
                    <td>
                        <div class="small"><i class="bi bi-envelope me-1"></i> <a href="mailto:{{ $sub->email }}">{{ $sub->email }}</a></div>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <a href="tel:{{ $sub->phone }}">{{ $sub->phone }}</a></div>
                    </td>
                    <td>
                        @if($sub->featuredProject)
                            <span class="badge bg-info text-dark">Project: {{ $sub->featuredProject->title }}</span>
                        @elseif($sub->property)
                            <span class="badge bg-primary">Property: {{ $sub->property->title }}</span>
                        @else
                            <span class="badge bg-secondary">General Enquiry</span>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $sub->created_at->format('M d, Y - h:i A') }}
                        <div class="text-xs text-muted">({{ $sub->created_at->diffForHumans() }})</div>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.submissions.show', $sub->id) }}" class="btn btn-sm btn-gold text-white me-1" title="View Submission">
                            <i class="bi bi-eye-fill me-1"></i> View
                        </a>
                        <form action="{{ route('admin.submissions.destroy', $sub->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                        No submissions received yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
</div>
@endsection
