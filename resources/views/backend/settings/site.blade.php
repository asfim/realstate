@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Site Settings</h4>
        <p class="text-muted small mb-0">Manage global site title, branding logos, favicon, and SEO meta configuration</p>
    </div>
    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> View Website
    </a>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ route('admin.settings.site') }}">
                
                <!-- BRANDING SETTINGS -->
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark">
                    <i class="bi bi-sliders me-2 text-warning"></i> General & Branding Settings
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-bold">Site Title</label>
                    <input type="text" class="form-control" name="site_title" value="{{ $settings['site_title'] ?? 'Aurelia Estates' }}" placeholder="e.g. Aurelia Estates" required>
                    <small class="text-muted">Used across header, footer, and default page title tag.</small>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Site Main Logo</label>
                        <input type="file" class="form-control mb-2" name="site_logo" accept="image/*">
                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                            <div class="p-2 border rounded bg-dark d-inline-block">
                                <img src="{{ asset_url($settings['site_logo']) }}" alt="Site Logo" style="max-height: 50px;">
                            </div>
                        @else
                            <small class="text-muted d-block">No custom site logo uploaded yet.</small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Favicon Icon (.ico, .png)</label>
                        <input type="file" class="form-control mb-2" name="site_favicon" accept="image/*,.ico">
                        @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                            <div class="p-2 border rounded bg-light d-inline-block">
                                <img src="{{ asset_url($settings['site_favicon']) }}" alt="Favicon" style="max-height: 32px; max-width: 32px;">
                            </div>
                        @else
                            <small class="text-muted d-block">No custom favicon uploaded yet.</small>
                        @endif
                    </div>
                </div>

                <!-- SEO META SETTINGS -->
                <h5 class="fw-bold border-bottom pb-3 mb-4 mt-2 text-dark">
                    <i class="bi bi-search me-2 text-warning"></i> Search Engine Optimization (SEO Meta)
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-bold">Meta Title Tag</label>
                    <input type="text" class="form-control" name="meta_title" value="{{ $settings['meta_title'] ?? 'Aurelia Estates | Find Your Dream Property' }}" placeholder="e.g. Aurelia Estates | Find Your Dream Property">
                    <small class="text-muted">The primary title displayed in browser tabs and search engine results.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Meta Keywords</label>
                    <input type="text" class="form-control" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? 'real estate, luxury properties, apartments, villas, commercial property' }}" placeholder="e.g. real estate, luxury properties, apartments, villas">
                    <small class="text-muted">Comma-separated list of keywords for search indexing.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="3" placeholder="Write a summary description for search engine listings...">{{ $settings['meta_description'] ?? 'Aurelia Estates — luxury real estate developer offering premium apartments, villas and commercial properties with verified listings, easy installments and legal support.' }}</textarea>
                    <small class="text-muted">Short summary of site content shown in Google/Bing search snippets.</small>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-gold px-4">
                        <i class="bi bi-save me-2"></i> Save Site Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
