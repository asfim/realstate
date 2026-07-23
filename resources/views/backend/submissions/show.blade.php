@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Submission Details</h4>
        <p class="text-muted small mb-0">Received on {{ $submission->created_at->format('F d, Y \a\t h:i A') }}</p>
    </div>
    <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Submissions
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                <i class="bi bi-chat-left-text me-2 text-warning"></i> Customer Message
            </h5>
            <div class="p-3 bg-light rounded border mb-4" style="font-size: 1.05rem; line-height: 1.7; white-space: pre-wrap;">{{ $submission->message ?? 'No message text provided.' }}</div>

            <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                <i class="bi bi-building me-2 text-warning"></i> Project / Property Context
            </h5>
            @if($submission->featuredProject)
                <div class="d-flex align-items-center p-3 border rounded bg-white">
                    @if($submission->featuredProject->image)
                        <img src="{{ asset_url($submission->featuredProject->image) }}" alt="Img" style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px;" class="me-3">
                    @endif
                    <div>
                        <h6 class="fw-bold mb-1">{{ $submission->featuredProject->title }}</h6>
                        <span class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $submission->featuredProject->location }}</span>
                        <div class="mt-1">
                            <a href="{{ route('project.featured', $submission->featuredProject->id) }}" target="_blank" class="small text-primary">View Project Page <i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                    </div>
                </div>
            @elseif($submission->property)
                <div class="d-flex align-items-center p-3 border rounded bg-white">
                    @if($submission->property->image)
                        <img src="{{ asset_url($submission->property->image) }}" alt="Img" style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px;" class="me-3">
                    @endif
                    <div>
                        <h6 class="fw-bold mb-1">{{ $submission->property->title }}</h6>
                        <span class="small text-muted"><i class="bi bi-tag"></i> {{ $submission->property->type }}</span>
                        <div class="mt-1">
                            <a href="{{ route('property.details', $submission->property->id) }}" target="_blank" class="small text-primary">View Property Page <i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-3 text-muted border rounded bg-light">General website enquiry</div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                <i class="bi bi-person-lines-fill me-2 text-warning"></i> Contact Customer
            </h5>

            <div class="mb-3">
                <label class="small text-muted d-block fw-bold text-uppercase">Full Name</label>
                <div class="fw-bold fs-5 text-dark">{{ $submission->name }}</div>
            </div>

            <div class="mb-3">
                <label class="small text-muted d-block fw-bold text-uppercase">Email Address</label>
                <div class="fw-bold"><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></div>
            </div>

            <div class="mb-4">
                <label class="small text-muted d-block fw-bold text-uppercase">Phone Number</label>
                <div class="fw-bold fs-6"><a href="tel:{{ $submission->phone }}">{{ $submission->phone }}</a></div>
            </div>

            <hr>

            <div class="d-grid gap-2">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $submission->phone) }}" target="_blank" class="btn btn-success">
                    <i class="bi bi-whatsapp me-2"></i> Chat on WhatsApp
                </a>
                <a href="mailto:{{ $submission->email }}?subject=RE: Enquiry for {{ $submission->featuredProject->title ?? $submission->property->title ?? 'Real Estate' }}" class="btn btn-outline-dark">
                    <i class="bi bi-envelope-fill me-2"></i> Reply by Email
                </a>
                <a href="tel:{{ $submission->phone }}" class="btn btn-outline-primary">
                    <i class="bi bi-telephone-fill me-2"></i> Call Customer
                </a>

                <form action="{{ route('admin.submissions.destroy', $submission->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i> Delete Submission
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
