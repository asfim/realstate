@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Edit Details: {{ $featuredProject->title }}</h4>
        <div class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $featuredProject->location }}</div>
    </div>
    <div>
        <a href="{{ route('project.featured', $featuredProject->id) }}" target="_blank" class="btn btn-outline-info btn-sm me-2">
            <i class="bi bi-eye me-1"></i> Preview Frontend
        </a>
        <a href="{{ route('admin.featured-project.details.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<form action="{{ route('admin.featured-project.details.update', $featuredProject->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card p-4 mb-4">
        <h5 class="fw-bold border-bottom pb-3 mb-4">Overview & Specification</h5>

        <div class="mb-3">
            <label class="form-label fw-bold">Project Overview Description</label>
            <textarea class="form-control" name="description" rows="4" placeholder="Enter detailed project description...">{{ old('description', $featuredProject->description) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Full Address</label>
                <input type="text" class="form-control" name="address" value="{{ old('address', $featuredProject->address) }}" placeholder="e.g. Plot # 17/A, Road # 126, Gulshan, Dhaka">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Land Area</label>
                <input type="text" class="form-control" name="land_area" value="{{ old('land_area', $featuredProject->land_area) }}" placeholder="e.g. 6.43 Katha">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Number of Floors</label>
                <input type="text" class="form-control" name="no_of_floors" value="{{ old('no_of_floors', $featuredProject->no_of_floors) }}" placeholder="e.g. G+9">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Apartments Per Floor</label>
                <input type="text" class="form-control" name="apartments_per_floor" value="{{ old('apartments_per_floor', $featuredProject->apartments_per_floor) }}" placeholder="e.g. 1">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Nearest Landmark</label>
                <input type="text" class="form-control" name="nearest_landmark" value="{{ old('nearest_landmark', $featuredProject->nearest_landmark) }}" placeholder="e.g. AMZ Hospital — 4 min walk">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Launch Date</label>
                <input type="text" class="form-control" name="launch_date" value="{{ old('launch_date', $featuredProject->launch_date) }}" placeholder="e.g. May 2024">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Expected Completion</label>
                <input type="text" class="form-control" name="expected_completion" value="{{ old('expected_completion', $featuredProject->expected_completion) }}" placeholder="e.g. Mar 2027">
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <h5 class="fw-bold border-bottom pb-3 mb-4">Media & Video Links</h5>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">YouTube / External Video URL</label>
                <input type="text" class="form-control" name="video_url" value="{{ old('video_url', $featuredProject->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                <div class="form-text small text-muted">Supports YouTube watch links, short links (youtu.be), or Vimeo.</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">OR Upload Video File (.mp4, .webm)</label>
                <input type="file" class="form-control" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                @if($featuredProject->video_file)
                    <div class="mt-2">
                        <video src="{{ Storage::url($featuredProject->video_file) }}" controls style="height: 100px; border-radius: 6px;" class="border"></video>
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="delete_video_file" value="1" id="del_vid_file">
                            <label class="form-check-label text-danger small fw-bold" for="del_vid_file">Remove Video File</label>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Video Cover / Poster Image</label>
                <input type="file" class="form-control" name="video_thumbnail" accept="image/*">
                @if($featuredProject->video_thumbnail)
                    <div class="mt-2">
                        <img src="{{ Storage::url($featuredProject->video_thumbnail) }}" alt="Poster" style="height: 80px; border-radius: 6px;" class="border">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="delete_video_thumbnail" value="1" id="del_vid_thumb">
                            <label class="form-check-label text-danger small fw-bold" for="del_vid_thumb">Remove Cover Image</label>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Map Embed Iframe / Directions URL</label>
                <input type="text" class="form-control" name="map_url" value="{{ old('map_url', $featuredProject->map_url) }}" placeholder="https://www.google.com/maps?q=...">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Brochure File (PDF / Doc)</label>
                <input type="file" class="form-control" name="brochure" accept=".pdf,.doc,.docx,.zip">
                @if($featuredProject->brochure)
                    <div class="mt-2 small text-muted">
                        Current Brochure: <a href="{{ Storage::url($featuredProject->brochure) }}" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i> Download Brochure</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Gallery Images</label>
            <input type="file" class="form-control mb-2" name="gallery[]" multiple accept="image/*">
            @if($featuredProject->gallery && count($featuredProject->gallery) > 0)
                <div class="row g-2 mt-2">
                    @foreach($featuredProject->gallery as $img)
                        <div class="col-auto text-center position-relative">
                            <img src="{{ asset_url($img) }}" style="width: 100px; height: 80px; object-fit: cover; border-radius: 8px;" class="border">
                            <div class="mt-1">
                                <label class="small text-danger" style="cursor: pointer;">
                                    <input type="checkbox" name="delete_gallery[]" value="{{ $img }}"> Delete
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold d-block mb-3">Floor Plan Category Images</label>
            <div class="row g-3">
                <!-- Ground Floor -->
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-layers me-1 text-warning"></i> Ground Floor Plan</div>
                        @if(isset($featuredProject->floor_plans['ground']['image']))
                            @php $gImg = $featuredProject->floor_plans['ground']['image']; @endphp
                            <div class="mb-2 text-center">
                                <img src="{{ asset_url($gImg) }}" style="height: 120px; width: 100%; object-fit: contain; background: #fff; border-radius: 6px;" class="border p-1">
                                <div class="form-check mt-1 text-start">
                                    <input class="form-check-input" type="checkbox" name="delete_floor_plan_ground" value="1" id="del_g">
                                    <label class="form-check-label text-danger small fw-bold" for="del_g">Remove Ground Floor Image</label>
                                </div>
                            </div>
                        @endif
                        <label class="form-label small text-muted">Upload Image for Ground Floor</label>
                        <input type="file" class="form-control form-control-sm mb-3" name="floor_plan_ground" accept="image/*">

                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark mb-0"><i class="bi bi-list-task me-1"></i> Floor Plan Specifications</label>
                                <button type="button" class="btn btn-xs btn-outline-primary add-spec-row" data-cat="ground" style="font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="bi bi-plus-circle me-1"></i> Add Row
                                </button>
                            </div>
                            <div id="spec-container-ground">
                                @php
                                    $gInfo = $featuredProject->floor_plans['ground']['info'] ?? [
                                        ['key' => 'Function', 'value' => 'Lobby & driveway'],
                                        ['key' => 'Ceiling height', 'value' => '12 ft'],
                                        ['key' => 'Parking bays', 'value' => '10'],
                                        ['key' => 'Generator room', 'value' => 'Included'],
                                    ];
                                @endphp
                                @foreach($gInfo as $i => $row)
                                    <div class="row g-2 mb-2 spec-row-item align-items-center">
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[ground][{{ $i }}][key]" value="{{ $row['key'] ?? '' }}" placeholder="Label">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[ground][{{ $i }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="Value">
                                        </div>
                                        <div class="col-2 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-spec-row" title="Delete Row"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typical Floor -->
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-layers me-1 text-warning"></i> Typical Floor Plan</div>
                        @if(isset($featuredProject->floor_plans['typical']['image']))
                            @php $tImg = $featuredProject->floor_plans['typical']['image']; @endphp
                            <div class="mb-2 text-center">
                                <img src="{{ asset_url($tImg) }}" style="height: 120px; width: 100%; object-fit: contain; background: #fff; border-radius: 6px;" class="border p-1">
                                <div class="form-check mt-1 text-start">
                                    <input class="form-check-input" type="checkbox" name="delete_floor_plan_typical" value="1" id="del_t">
                                    <label class="form-check-label text-danger small fw-bold" for="del_t">Remove Typical Floor Image</label>
                                </div>
                            </div>
                        @endif
                        <label class="form-label small text-muted">Upload Image for Typical Floor</label>
                        <input type="file" class="form-control form-control-sm mb-3" name="floor_plan_typical" accept="image/*">

                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark mb-0"><i class="bi bi-list-task me-1"></i> Floor Plan Specifications</label>
                                <button type="button" class="btn btn-xs btn-outline-primary add-spec-row" data-cat="typical" style="font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="bi bi-plus-circle me-1"></i> Add Row
                                </button>
                            </div>
                            <div id="spec-container-typical">
                                @php
                                    $tInfo = $featuredProject->floor_plans['typical']['info'] ?? [
                                        ['key' => 'Function', 'value' => 'Single 3-bed apartment'],
                                        ['key' => 'Carpet area', 'value' => '3,050 sft'],
                                        ['key' => 'Balconies', 'value' => '2'],
                                        ['key' => 'Orientation', 'value' => 'North-East'],
                                    ];
                                @endphp
                                @foreach($tInfo as $i => $row)
                                    <div class="row g-2 mb-2 spec-row-item align-items-center">
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[typical][{{ $i }}][key]" value="{{ $row['key'] ?? '' }}" placeholder="Label">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[typical][{{ $i }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="Value">
                                        </div>
                                        <div class="col-2 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-spec-row" title="Delete Row"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roof Floor -->
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-layers me-1 text-warning"></i> Roof Floor Plan</div>
                        @if(isset($featuredProject->floor_plans['roof']['image']))
                            @php $rImg = $featuredProject->floor_plans['roof']['image']; @endphp
                            <div class="mb-2 text-center">
                                <img src="{{ asset_url($rImg) }}" style="height: 120px; width: 100%; object-fit: contain; background: #fff; border-radius: 6px;" class="border p-1">
                                <div class="form-check mt-1 text-start">
                                    <input class="form-check-input" type="checkbox" name="delete_floor_plan_roof" value="1" id="del_r">
                                    <label class="form-check-label text-danger small fw-bold" for="del_r">Remove Roof Floor Image</label>
                                </div>
                            </div>
                        @endif
                        <label class="form-label small text-muted">Upload Image for Roof Floor</label>
                        <input type="file" class="form-control form-control-sm mb-3" name="floor_plan_roof" accept="image/*">

                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark mb-0"><i class="bi bi-list-task me-1"></i> Floor Plan Specifications</label>
                                <button type="button" class="btn btn-xs btn-outline-primary add-spec-row" data-cat="roof" style="font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="bi bi-plus-circle me-1"></i> Add Row
                                </button>
                            </div>
                            <div id="spec-container-roof">
                                @php
                                    $rInfo = $featuredProject->floor_plans['roof']['info'] ?? [
                                        ['key' => 'Function', 'value' => 'Rooftop walkway & lounge'],
                                        ['key' => 'Open area', 'value' => '1,800 sft'],
                                        ['key' => 'Features', 'value' => 'Pool deck, garden'],
                                        ['key' => 'Access', 'value' => 'Private lift'],
                                    ];
                                @endphp
                                @foreach($rInfo as $i => $row)
                                    <div class="row g-2 mb-2 spec-row-item align-items-center">
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[roof][{{ $i }}][key]" value="{{ $row['key'] ?? '' }}" placeholder="Label">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[roof][{{ $i }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="Value">
                                        </div>
                                        <div class="col-2 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-spec-row" title="Delete Row"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unit Plan -->
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-layers me-1 text-warning"></i> Unit Plan</div>
                        @if(isset($featuredProject->floor_plans['unit']['image']))
                            @php $uImg = $featuredProject->floor_plans['unit']['image']; @endphp
                            <div class="mb-2 text-center">
                                <img src="{{ asset_url($uImg) }}" style="height: 120px; width: 100%; object-fit: contain; background: #fff; border-radius: 6px;" class="border p-1">
                                <div class="form-check mt-1 text-start">
                                    <input class="form-check-input" type="checkbox" name="delete_floor_plan_unit" value="1" id="del_u">
                                    <label class="form-check-label text-danger small fw-bold" for="del_u">Remove Unit Plan Image</label>
                                </div>
                            </div>
                        @endif
                        <label class="form-label small text-muted">Upload Image for Unit Plan</label>
                        <input type="file" class="form-control form-control-sm mb-3" name="floor_plan_unit" accept="image/*">

                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark mb-0"><i class="bi bi-list-task me-1"></i> Floor Plan Specifications</label>
                                <button type="button" class="btn btn-xs btn-outline-primary add-spec-row" data-cat="unit" style="font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="bi bi-plus-circle me-1"></i> Add Row
                                </button>
                            </div>
                            <div id="spec-container-unit">
                                @php
                                    $uInfo = $featuredProject->floor_plans['unit']['info'] ?? [
                                        ['key' => 'Bedrooms', 'value' => '3'],
                                        ['key' => 'Bathrooms', 'value' => '3'],
                                        ['key' => 'Living + dining', 'value' => '620 sft'],
                                        ['key' => 'Kitchen', 'value' => '180 sft'],
                                    ];
                                @endphp
                                @foreach($uInfo as $i => $row)
                                    <div class="row g-2 mb-2 spec-row-item align-items-center">
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[unit][{{ $i }}][key]" value="{{ $row['key'] ?? '' }}" placeholder="Label">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm" name="floor_plan_info[unit][{{ $i }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="Value">
                                        </div>
                                        <div class="col-2 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-spec-row" title="Delete Row"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-gold px-4">
            <i class="bi bi-save me-1"></i> Save Details
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".add-spec-row").forEach(function(btn) {
        btn.addEventListener("click", function() {
            const cat = this.getAttribute("data-cat");
            const container = document.getElementById("spec-container-" + cat);
            const index = Date.now();

            const rowDiv = document.createElement("div");
            rowDiv.className = "row g-2 mb-2 spec-row-item align-items-center";
            rowDiv.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="floor_plan_info[${cat}][${index}][key]" placeholder="Label">
                </div>
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="floor_plan_info[${cat}][${index}][value]" placeholder="Value">
                </div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-spec-row" title="Delete Row"><i class="bi bi-trash"></i></button>
                </div>
            `;
            container.appendChild(rowDiv);
        });
    });

    document.addEventListener("click", function(e) {
        const removeBtn = e.target.closest(".remove-spec-row");
        if (removeBtn) {
            const rowItem = removeBtn.closest(".spec-row-item");
            if (rowItem) {
                rowItem.remove();
            }
        }
    });
});
</script>
@endpush
