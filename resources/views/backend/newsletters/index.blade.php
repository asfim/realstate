@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Newsletter Subscribers</h4>
        <p class="text-muted small mb-0">Manage website email newsletter subscribers</p>
    </div>
    <div>
        <span class="badge bg-gold px-3 py-2 fs-6">
            <i class="bi bi-people-fill me-1"></i> Total Subscribers: {{ $totalSubscribers }}
        </span>
    </div>
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-envelope-at-fill text-warning me-2"></i> Subscriber Email List</h5>
        
        <form action="{{ route('admin.newsletters.index') }}" method="GET" class="d-flex gap-2" style="max-width: 320px;">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search email..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-navy btn-sm px-3"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('admin.newsletters.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>

    @if($subscribers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Email Address</th>
                        <th>Subscribed At</th>
                        <th style="width: 120px;" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscribers as $index => $subscriber)
                    <tr>
                        <td>{{ $subscribers->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">
                            <i class="bi bi-envelope me-2 text-primary"></i> {{ $subscriber->email }}
                        </td>
                        <td class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i> {{ $subscriber->created_at->format('M d, Y h:i A') }}
                            <span class="text-muted">({{ $subscriber->created_at->diffForHumans() }})</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.newsletters.destroy', $subscriber->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this subscriber?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Subscriber">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $subscribers->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 mb-3 d-block text-secondary"></i>
            <h6>No newsletter subscribers found.</h6>
        </div>
    @endif
</div>
@endsection
