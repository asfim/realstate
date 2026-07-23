@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">About Us Page Settings</h4>
        <p class="text-muted small mb-0">Manage main story, mission, vision, image, and statistical counter blocks</p>
    </div>
</div>

<div class="card p-4">
    <form action="{{ route('admin.about_settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-body-text me-2 text-warning"></i> Page Header & Intro Story</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Page Header Title</label>
                <input type="text" class="form-control" name="about_title" value="{{ $settings['about_title'] ?? 'About Aurelia Estates' }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Page Header Subtitle</label>
                <input type="text" class="form-control" name="about_subtitle" value="{{ $settings['about_subtitle'] ?? 'Two decades of excellence in luxury real estate.' }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Eyebrow Tag</label>
                <input type="text" class="form-control" name="about_eyebrow" value="{{ $settings['about_eyebrow'] ?? 'About Aurelia Estates' }}">
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label fw-bold">Section Heading Title *</label>
                <input type="text" class="form-control" name="about_heading" value="{{ $settings['about_heading'] ?? 'Building More Than Properties — Building Trust' }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Main Story Description *</label>
            <textarea class="form-control" name="about_description" rows="4" required>{{ $settings['about_description'] ?? 'Since 2010, Aurelia Estates has delivered over 500 residential and commercial projects across the country, guided by one principle: every buyer deserves clarity, quality, and a home worth the investment.' }}</textarea>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <label class="form-label fw-bold">Main Image</label>
                <input type="file" class="form-control mb-2" name="about_image" accept="image/*">
                @if(setting('about_image'))
                    <div class="mt-2">
                        <img src="{{ asset_url(setting('about_image')) }}" alt="About Image" style="height: 120px; border-radius: 8px;" class="border">
                    </div>
                @endif
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Image Badge Number</label>
                <input type="text" class="form-control" name="about_badge_number" value="{{ $settings['about_badge_number'] ?? '20+' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Badge Text</label>
                <input type="text" class="form-control" name="about_badge_text" value="{{ $settings['about_badge_text'] ?? 'Years shaping city skylines' }}">
            </div>
        </div>

        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark mt-4"><i class="bi bi-compass me-2 text-warning"></i> Mission & Vision</h5>
        <div class="row mb-4">
            <div class="col-md-6 border-end">
                <div class="mb-2">
                    <label class="form-label fw-bold">Mission Title</label>
                    <input type="text" class="form-control" name="about_mission_title" value="{{ $settings['about_mission_title'] ?? 'Our Mission' }}">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Mission Description</label>
                    <textarea class="form-control" name="about_mission_text" rows="3">{{ $settings['about_mission_text'] ?? 'Make premium real estate accessible through transparent, verified transactions.' }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <label class="form-label fw-bold">Vision Title</label>
                    <input type="text" class="form-control" name="about_vision_title" value="{{ $settings['about_vision_title'] ?? 'Our Vision' }}">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Vision Description</label>
                    <textarea class="form-control" name="about_vision_text" rows="3">{{ $settings['about_vision_text'] ?? 'To be the most trusted name in luxury real estate development nationwide.' }}</textarea>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark mt-4"><i class="bi bi-bar-chart-line me-2 text-warning"></i> Statistical Counter Blocks</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label small fw-bold">Stat 1 (Projects)</label>
                    <input type="number" class="form-control form-control-sm mb-1" name="about_stat1_number" value="{{ $settings['about_stat1_number'] ?? 500 }}" placeholder="Number (e.g. 500)">
                    <input type="text" class="form-control form-control-sm mb-1" name="about_stat1_suffix" value="{{ $settings['about_stat1_suffix'] ?? '+' }}" placeholder="Suffix (+, %)">
                    <input type="text" class="form-control form-control-sm" name="about_stat1_label" value="{{ $settings['about_stat1_label'] ?? 'Projects' }}" placeholder="Label">
                </div>
            </div>
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label small fw-bold">Stat 2 (Customers)</label>
                    <input type="number" class="form-control form-control-sm mb-1" name="about_stat2_number" value="{{ $settings['about_stat2_number'] ?? 12000 }}" placeholder="Number">
                    <input type="text" class="form-control form-control-sm mb-1" name="about_stat2_suffix" value="{{ $settings['about_stat2_suffix'] ?? '+' }}" placeholder="Suffix">
                    <input type="text" class="form-control form-control-sm" name="about_stat2_label" value="{{ $settings['about_stat2_label'] ?? 'Customers' }}" placeholder="Label">
                </div>
            </div>
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label small fw-bold">Stat 3 (Employees)</label>
                    <input type="number" class="form-control form-control-sm mb-1" name="about_stat3_number" value="{{ $settings['about_stat3_number'] ?? 350 }}" placeholder="Number">
                    <input type="text" class="form-control form-control-sm mb-1" name="about_stat3_suffix" value="{{ $settings['about_stat3_suffix'] ?? '+' }}" placeholder="Suffix">
                    <input type="text" class="form-control form-control-sm" name="about_stat3_label" value="{{ $settings['about_stat3_label'] ?? 'Employees' }}" placeholder="Label">
                </div>
            </div>
            <div class="col-md-3">
                <div class="border p-3 rounded bg-light">
                    <label class="form-label small fw-bold">Stat 4 (Satisfaction)</label>
                    <input type="number" class="form-control form-control-sm mb-1" name="about_stat4_number" value="{{ $settings['about_stat4_number'] ?? 98 }}" placeholder="Number">
                    <input type="text" class="form-control form-control-sm mb-1" name="about_stat4_suffix" value="{{ $settings['about_stat4_suffix'] ?? '%' }}" placeholder="Suffix">
                    <input type="text" class="form-control form-control-sm" name="about_stat4_label" value="{{ $settings['about_stat4_label'] ?? 'Client Satisfaction' }}" placeholder="Label">
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Save About Settings</button>
        </div>
    </form>
</div>
@endsection
