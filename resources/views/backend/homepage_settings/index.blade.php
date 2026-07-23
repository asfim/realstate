@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Homepage Settings</h4>
    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> View Website
    </a>
</div>

<div class="row">
    <!-- Navigation Tabs -->
    <div class="col-md-3 mb-4">
        <div class="card p-2">
            <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link active text-start mb-1" id="hero-tab" data-bs-toggle="pill" data-bs-target="#hero" type="button" role="tab" aria-selected="true">
                    <i class="bi bi-image me-2"></i> Hero Section
                </button>
                <button class="nav-link text-start mb-1" id="search-tab" data-bs-toggle="pill" data-bs-target="#search" type="button" role="tab" aria-selected="false">
                    <i class="bi bi-search me-2"></i> Property Search
                </button>
                <button class="nav-link text-start mb-1" id="cta-tab" data-bs-toggle="pill" data-bs-target="#cta" type="button" role="tab" aria-selected="false">
                    <i class="bi bi-megaphone me-2"></i> CTA Section
                </button>

                <hr>
                <div class="text-muted small fw-bold px-3 py-2 text-uppercase">Dynamic Sections</div>
                
                <!-- Links to Future CRUD pages -->
                <a href="{{ route('admin.team-members.index') }}" class="nav-link text-start mb-1 text-muted">
                    <i class="bi bi-people me-2"></i> Team Members <i class="bi bi-box-arrow-up-right float-end"></i>
                </a>
                <a href="{{ route('admin.testimonials.index') }}" class="nav-link text-start mb-1 text-muted">
                    <i class="bi bi-chat-quote me-2"></i> Testimonials <i class="bi bi-box-arrow-up-right float-end"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="col-md-9">
        <div class="card p-4">
            <form action="{{ route('admin.homepage_settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="tab-content" id="settings-tabContent">
                    
                    <!-- HERO SECTION -->
                    <div class="tab-pane fade show active" id="hero" role="tabpanel" tabindex="0">
                        <h5 class="fw-bold border-bottom pb-3 mb-4">Hero Section Settings</h5>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="hero_status" name="hero_status" value="1" {{ ($settings['hero_status'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="hero_status">Enable Hero Section</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hero Title</label>
                            <input type="text" class="form-control" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Highlighted Text (Gold)</label>
                            <input type="text" class="form-control" name="hero_highlight" value="{{ $settings['hero_highlight'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="2">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" class="form-control" name="hero_btn_text" value="{{ $settings['hero_btn_text'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button URL</label>
                                <input type="text" class="form-control" name="hero_btn_url" value="{{ $settings['hero_btn_url'] ?? '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Image</label>
                            <input type="file" class="form-control" name="hero_bg_image" accept="image/*">
                            @if(isset($settings['hero_bg_image']))
                                <div class="mt-2">
                                    <img src="{{ Storage::url($settings['hero_bg_image']) }}" alt="Hero BG" style="height: 100px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- PROPERTY SEARCH SECTION -->
                    <div class="tab-pane fade" id="search" role="tabpanel" tabindex="0">
                        <h5 class="fw-bold border-bottom pb-3 mb-4">Property Search Section Settings</h5>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="property_search_status" name="property_search_status" value="1" {{ ($settings['property_search_status'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="property_search_status">Enable Search Section</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Heading</label>
                            <input type="text" class="form-control" name="search_heading" value="{{ $settings['search_heading'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Image</label>
                            <input type="file" class="form-control" name="search_bg_image" accept="image/*">
                            @if(isset($settings['search_bg_image']))
                                <div class="mt-2">
                                    <img src="{{ Storage::url($settings['search_bg_image']) }}" alt="Search BG" style="height: 100px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- CTA SECTION -->
                    <div class="tab-pane fade" id="cta" role="tabpanel" tabindex="0">
                        <h5 class="fw-bold border-bottom pb-3 mb-4">CTA Section Settings</h5>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="cta_status" name="cta_status" value="1" {{ ($settings['cta_status'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="cta_status">Enable CTA Section</label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="cta_title" value="{{ $settings['cta_title'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="cta_description" rows="3">{{ $settings['cta_description'] ?? '' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" class="form-control" name="cta_btn_text" value="{{ $settings['cta_btn_text'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Link</label>
                                <input type="text" class="form-control" name="cta_btn_link" value="{{ $settings['cta_btn_link'] ?? '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Image</label>
                            <input type="file" class="form-control" name="cta_bg_image" accept="image/*">
                            @if(isset($settings['cta_bg_image']))
                                <div class="mt-2">
                                    <img src="{{ Storage::url($settings['cta_bg_image']) }}" alt="CTA BG" style="height: 100px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>
                    </div>



                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-gold px-4">
                        <i class="bi bi-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let hash = window.location.hash;
        if (hash) {
            let targetTab = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (targetTab) {
                let tab = new bootstrap.Tab(targetTab);
                tab.show();
            }
        }

        // Update URL hash when a tab is clicked
        let tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(function(button) {
            button.addEventListener('shown.bs.tab', function (e) {
                window.location.hash = e.target.getAttribute('data-bs-target');
            });
        });
    });
</script>
@endpush
