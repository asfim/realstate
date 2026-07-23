@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Footer Settings</h4>
    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> View Website
    </a>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ route('admin.settings.footer') }}">
                
                <h5 class="fw-bold border-bottom pb-3 mb-4">Footer Configuration</h5>
                
                <div class="mb-3">
                    <label class="form-label">Footer Logo</label>
                    <input type="file" class="form-control" name="footer_logo" accept="image/*">
                    @if(isset($settings['footer_logo']))
                        <div class="mt-2 bg-dark p-2" style="border-radius: 8px; display: inline-block;">
                            <img src="{{ Storage::url($settings['footer_logo']) }}" alt="Footer Logo" style="height: 60px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="footer_description" rows="3">{{ $settings['footer_description'] ?? '' }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="footer_phone" value="{{ $settings['footer_phone'] ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="footer_email" value="{{ $settings['footer_email'] ?? '' }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="footer_address" value="{{ $settings['footer_address'] ?? '' }}">
                </div>

                <h6 class="fw-bold mb-3">Social Links</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-facebook text-primary"></i> Facebook</label>
                        <input type="url" class="form-control" name="footer_facebook" value="{{ $settings['footer_facebook'] ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-instagram text-danger"></i> Instagram</label>
                        <input type="url" class="form-control" name="footer_instagram" value="{{ $settings['footer_instagram'] ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-linkedin text-info"></i> LinkedIn</label>
                        <input type="url" class="form-control" name="footer_linkedin" value="{{ $settings['footer_linkedin'] ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-youtube text-danger"></i> YouTube</label>
                        <input type="url" class="form-control" name="footer_youtube" value="{{ $settings['footer_youtube'] ?? '' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Copyright Text</label>
                    <input type="text" class="form-control" name="footer_copyright" value="{{ $settings['footer_copyright'] ?? '' }}">
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
