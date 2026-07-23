@extends('frontend.layouts.app')

@section('content')
<div class="construction-page pt-5" style="background-color: #1a1a1a; min-height: 100vh; padding-bottom: 80px;">
  <div class="container" style="padding-top: 120px;">
    
    <!-- Top Section -->
    <div class="row align-items-center mb-5">
      <!-- Left Info -->
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h1 style="color: #fff; font-family: var(--font-head); font-size: 3.5rem; margin-bottom: 5px;">{{ $property->title }}</h1>
        <p style="color: #a0a0a0; font-size: 1.1rem; margin-bottom: 40px;"><i class="fa-solid fa-location-dot me-2 text-warning"></i>{{ $property->location }}</p>
        
        <div class="d-flex flex-wrap gap-4 mb-4">
          <div class="status-card">
            <span style="color: #888; font-size: 0.9rem;">Expected Completion Date</span>
            <h5 style="color: #fff; margin-top: 5px;">{{ $property->detail->expected_completion ?? 'N/A' }}</h5>
          </div>
          <div class="status-card">
            <span style="color: #888; font-size: 0.9rem;">Status Updated</span>
            <h5 style="color: #fff; margin-top: 5px;">{{ $property->constructionStatus->status_updated_at ?? 'N/A' }}</h5>
          </div>
        </div>
        
        <a href="{{ route('property.details', $property->id) }}" style="color: var(--gold); text-decoration: none; font-weight: 500;"><i class="fa-solid fa-arrow-left me-2"></i>Back to details page</a>
      </div>
      
      <!-- Right Image -->
      <div class="col-lg-6">
        <div class="cs-image-wrapper" style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
          <img src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75' }}" alt="{{ $property->title }} Project" style="width: 100%; height: auto; object-fit: cover;">
          <div style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 8px;">
            <strong style="color: var(--navy); font-family: var(--font-head);">{{ $property->title }}</strong>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Table Section -->
    <div class="cs-table-card" style="background: #222; border-radius: 16px; padding: 40px; margin-top: 60px;">
      <h3 style="color: #fff; font-family: var(--font-head); margin-bottom: 10px;">Construction progress details</h3>
      <p style="color: #888; font-size: 0.9rem; margin-bottom: 30px;">The progress table is updated regularly by our engineering team.</p>
      
      <div class="table-responsive">
        <table class="table table-borderless cs-table" style="color: #ddd;">
          <thead>
            <tr style="background: rgba(184, 134, 11, 0.2); border-radius: 8px;">
              <th style="padding: 15px 20px; color: #fff; font-weight: 600; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">SL. No</th>
              <th style="padding: 15px 20px; color: #fff; font-weight: 600;">Name of the work</th>
              <th style="padding: 15px 20px; color: #fff; font-weight: 600; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">Progress details</th>
            </tr>
          </thead>
          <tbody>
            @forelse($property->constructionStatus->progress_items ?? [] as $index => $item)
              <tr>
                <td style="padding: 15px 20px;">{{ sprintf('%02d', $index + 1) }}</td>
                <td style="padding: 15px 20px;">{{ $item['name'] }}</td>
                <td style="padding: 15px 20px;">{{ $item['progress'] }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted" style="padding: 20px;">No progress items listed yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
  </div>
</div>

<style>
  .status-card {
    background: #2a2a2a;
    padding: 20px 30px;
    border-radius: 12px;
    min-width: 200px;
    border: 1px solid rgba(255,255,255,0.05);
  }
  .cs-table tbody tr {
    border-bottom: 1px solid rgba(255,255,255,0.05);
  }
  .cs-table tbody tr:nth-of-type(odd) {
    background-color: rgba(255, 255, 255, 0.02);
  }
  .cs-table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05);
  }
  .cs-table td {
    vertical-align: middle;
  }
</style>
@endsection
