@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Manage Details & Status: <span class="text-primary">{{ $property->title }}</span></h4>
        <span class="text-muted small">Update rich specifications, media galleries, layouts, and construction milestones.</span>
    </div>
    <a href="{{ route('admin.properties.manage.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-pills mb-4" id="manageTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="details-tab" data-bs-toggle="pill" data-bs-target="#details-tab-pane" type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">
            <i class="bi bi-file-earmark-text me-1"></i> Property Details
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="construction-tab" data-bs-toggle="pill" data-bs-target="#construction-tab-pane" type="button" role="tab" aria-controls="construction-tab-pane" aria-selected="false">
            <i class="bi bi-cone-striped me-1"></i> Construction Status
        </button>
    </li>
</ul>

<div class="tab-content" id="manageTabsContent">
    <!-- TAB 1: PROPERTY DETAILS -->
    <div class="tab-pane fade show active card p-4" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
        <form action="{{ route('admin.properties.updateDetails', $property->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h5 class="fw-bold border-bottom pb-2 mb-3">Refined details & layouts</h5>

            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="4" required placeholder="e.g. Blessed with an incredible number of conveniences...">{{ old('description', $detail->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Location (e.g. Mohakhali, Dhaka)</label>
                    <input type="text" class="form-control" name="location" value="{{ old('location', $property->location) }}" placeholder="e.g. Mohakhali, Dhaka">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Price (e.g. Contact for Price)</label>
                    <input type="text" class="form-control" name="price" value="{{ old('price', $property->price) }}" placeholder="e.g. Contact for Price">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Bedrooms</label>
                    <input type="number" class="form-control" name="beds" value="{{ old('beds', $property->beds) }}" placeholder="e.g. 3">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Bathrooms</label>
                    <input type="number" class="form-control" name="baths" value="{{ old('baths', $property->baths) }}" placeholder="e.g. 3">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Area (sqft)</label>
                    <input type="number" class="form-control" name="area" value="{{ old('area', $property->area) }}" placeholder="e.g. 2100">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address', $detail->address) }}" placeholder="e.g. Plot-12, Abbas Garden Road, Mohakhali, Dhaka">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Land Area</label>
                    <input type="text" class="form-control" name="land_area" value="{{ old('land_area', $detail->land_area) }}" placeholder="e.g. 8 Katha">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Number of Floors</label>
                    <input type="text" class="form-control" name="no_of_floors" value="{{ old('no_of_floors', $detail->no_of_floors) }}" placeholder="e.g. B+G+7">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Apartment / Floor</label>
                    <input type="text" class="form-control" name="apartments_per_floor" value="{{ old('apartments_per_floor', $detail->apartments_per_floor) }}" placeholder="e.g. 2">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Launch Date</label>
                    <input type="text" class="form-control" name="launch_date" value="{{ old('launch_date', $detail->launch_date) }}" placeholder="e.g. October 2023">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Expected Completion</label>
                    <input type="text" class="form-control" name="expected_completion" value="{{ old('expected_completion', $detail->expected_completion) }}" placeholder="e.g. September 2026">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Google Maps embed URL (iframe src)</label>
                <input type="text" class="form-control" name="map_url" value="{{ old('map_url', $detail->map_url) }}" placeholder="e.g. https://www.google.com/maps/embed?...">
            </div>

            <!-- Gallery Images -->
            <div class="card p-3 bg-light mb-3">
                <label class="form-label fw-bold mb-2">Gallery Images (Upload multiple)</label>
                <input type="file" class="form-control mb-3" name="gallery[]" accept="image/*" multiple>
                
                @if(!empty($detail->gallery))
                    <div class="row g-2">
                        @foreach($detail->gallery as $imgPath)
                            <div class="col-md-2 col-4 text-center position-relative">
                                <img src="{{ Storage::url($imgPath) }}" alt="Gallery Img" class="w-100 rounded" style="height: 80px; object-fit: cover;">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="delete_gallery[]" value="{{ $imgPath }}" id="del_gal_{{ $loop->index }}">
                                    <label class="form-check-label small text-danger" for="del_gal_{{ $loop->index }}">Delete</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Floor Plan Images -->
            <div class="card p-3 bg-light mb-4">
                <label class="form-label fw-bold mb-2">Floor Plan Images (Upload multiple)</label>
                <input type="file" class="form-control mb-3" name="floor_plans[]" accept="image/*" multiple>
                
                @if(!empty($detail->floor_plans))
                    <div class="row g-2">
                        @foreach($detail->floor_plans as $imgPath)
                            <div class="col-md-2 col-4 text-center position-relative">
                                <img src="{{ Storage::url($imgPath) }}" alt="Floor plan" class="w-100 rounded" style="height: 80px; object-fit: cover;">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="delete_floor_plans[]" value="{{ $imgPath }}" id="del_fp_{{ $loop->index }}">
                                    <label class="form-check-label small text-danger" for="del_fp_{{ $loop->index }}">Delete</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-navy"><i class="bi bi-save me-1"></i> Update Property Details</button>
        </form>
    </div>

    <!-- TAB 2: CONSTRUCTION STATUS -->
    <div class="tab-pane fade card p-4" id="construction-tab-pane" role="tabpanel" aria-labelledby="construction-tab" tabindex="0">
        <form action="{{ route('admin.properties.updateConstruction', $property->id) }}" method="POST">
            @csrf
            
            <h5 class="fw-bold border-bottom pb-2 mb-3">Construction status & progress details</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Expected Completion Date (from Details)</label>
                    <input type="text" class="form-control" value="{{ $detail->expected_completion ?? 'Not set yet' }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Updated Month/Year</label>
                    <input type="text" class="form-control" name="status_updated_at" value="{{ old('status_updated_at', $construction->status_updated_at) }}" placeholder="e.g. June, 2026">
                </div>
            </div>

            <div class="card p-3 bg-light mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label fw-bold mb-0">Progress Details Checklist</label>
                    <button type="button" class="btn btn-sm btn-navy" id="add-progress-item">
                        <i class="bi bi-plus-lg"></i> Add Row
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="progress-table">
                        <thead class="table-secondary">
                            <tr>
                                <th>Name of the Work</th>
                                <th style="width: 250px;">Progress details (e.g. 100%, 95%, Not Yet Started)</th>
                                <th style="width: 100px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="progress-tbody">
                            @forelse($construction->progress_items ?? [] as $item)
                                <tr>
                                    <td>
                                        <input type="text" name="progress_names[]" class="form-control" value="{{ $item['name'] ?? '' }}" placeholder="e.g. Foundation work">
                                    </td>
                                    <td>
                                        <input type="text" name="progress_values[]" class="form-control" value="{{ $item['progress'] ?? '' }}" placeholder="e.g. 100% or Not Yet Started">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-progress-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td>
                                        <input type="text" name="progress_names[]" class="form-control" placeholder="e.g. Foundation work">
                                    </td>
                                    <td>
                                        <input type="text" name="progress_values[]" class="form-control" placeholder="e.g. 100% or Not Yet Started">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-progress-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-navy"><i class="bi bi-save me-1"></i> Update Construction Status</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Manage redirect tab hash on load
        let hash = window.location.hash;
        if (hash) {
            let targetTab = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (targetTab) {
                // Remove active class from details tab
                document.getElementById('details-tab').classList.remove('active');
                document.getElementById('details-tab-pane').classList.remove('show', 'active');
                
                // Add active class to target tab
                targetTab.classList.add('active');
                let targetPane = document.querySelector(hash);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            }
        }

        // Keep hash on tab change
        let tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(function(button) {
            button.addEventListener('shown.bs.tab', function(e) {
                window.location.hash = e.target.getAttribute('data-bs-target');
            });
        });

        // Progress row repeater
        const addBtn = document.getElementById('add-progress-item');
        const tbody = document.getElementById('progress-tbody');

        addBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="progress_names[]" class="form-control" placeholder="e.g. Foundation work">
                </td>
                <td>
                    <input type="text" name="progress_values[]" class="form-control" placeholder="e.g. 100% or Not Yet Started">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-progress-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-progress-row')) {
                const tr = e.target.closest('tr');
                if (tbody.children.length > 1) {
                    tr.remove();
                } else {
                    tr.querySelectorAll('input').forEach(input => input.value = '');
                }
            }
        });
    });
</script>
@endpush
@endsection
