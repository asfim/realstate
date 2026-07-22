@extends('frontend.layouts.app')

@section('content')
<div class="construction-page pt-5" style="background-color: #1a1a1a; min-height: 100vh; padding-bottom: 80px;">
  <div class="container" style="padding-top: 120px;">
    
    <!-- Top Section -->
    <div class="row align-items-center mb-5">
      <!-- Left Info -->
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h1 style="color: #fff; font-family: var(--font-head); font-size: 3.5rem; margin-bottom: 5px;">Amabilia</h1>
        <p style="color: #a0a0a0; font-size: 1.1rem; margin-bottom: 40px;"><i class="fa-solid fa-location-dot me-2 text-warning"></i>Mohakhali, Dhaka</p>
        
        <div class="d-flex flex-wrap gap-4 mb-4">
          <div class="status-card">
            <span style="color: #888; font-size: 0.9rem;">Expected Completion Date</span>
            <h5 style="color: #fff; margin-top: 5px;">September 2026</h5>
          </div>
          <div class="status-card">
            <span style="color: #888; font-size: 0.9rem;">Status Updated</span>
            <h5 style="color: #fff; margin-top: 5px;">June, 2026</h5>
          </div>
        </div>
        
        <a href="{{ route('projects') }}" style="color: var(--gold); text-decoration: none; font-weight: 500;"><i class="fa-solid fa-arrow-left me-2"></i>Back to all projects</a>
      </div>
      
      <!-- Right Image -->
      <div class="col-lg-6">
        <div class="cs-image-wrapper" style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
          <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75" alt="Amabilia Project" style="width: 100%; height: auto; object-fit: cover;">
          <div style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 8px;">
            <strong style="color: var(--navy); font-family: var(--font-head);">Amabilia</strong>
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
            <tr><td style="padding: 15px 20px;">01</td><td style="padding: 15px 20px;">Foundation work</td><td style="padding: 15px 20px;">100%</td></tr>
            <tr><td style="padding: 15px 20px;">02</td><td style="padding: 15px 20px;">Super Structure work</td><td style="padding: 15px 20px;">100%</td></tr>
            <tr><td style="padding: 15px 20px;">03</td><td style="padding: 15px 20px;">Block work</td><td style="padding: 15px 20px;">100%</td></tr>
            <tr><td style="padding: 15px 20px;">04</td><td style="padding: 15px 20px;">Panel Work</td><td style="padding: 15px 20px;">100%</td></tr>
            <tr><td style="padding: 15px 20px;">05</td><td style="padding: 15px 20px;">Electrical conducing work</td><td style="padding: 15px 20px;">95%</td></tr>
            <tr><td style="padding: 15px 20px;">06</td><td style="padding: 15px 20px;">Door frame fitting work</td><td style="padding: 15px 20px;">100%</td></tr>
            <tr><td style="padding: 15px 20px;">07</td><td style="padding: 15px 20px;">Sanitary & plumbing line work</td><td style="padding: 15px 20px;">98%</td></tr>
            <tr><td style="padding: 15px 20px;">08</td><td style="padding: 15px 20px;">Inside Plastering work</td><td style="padding: 15px 20px;">98%</td></tr>
            <tr><td style="padding: 15px 20px;">09</td><td style="padding: 15px 20px;">Electrical cable wiring work</td><td style="padding: 15px 20px;">95%</td></tr>
            <tr><td style="padding: 15px 20px;">10</td><td style="padding: 15px 20px;">Wall Tiles & floor tiles work</td><td style="padding: 15px 20px;">95%</td></tr>
            <tr><td style="padding: 15px 20px;">11</td><td style="padding: 15px 20px;">Common sanitary & Plumbing line work</td><td style="padding: 15px 20px;">70%</td></tr>
            <tr><td style="padding: 15px 20px;">12</td><td style="padding: 15px 20px;">Outside plastering work</td><td style="padding: 15px 20px;">95%</td></tr>
            <tr><td style="padding: 15px 20px;">13</td><td style="padding: 15px 20px;">Floor tiles/Marble work</td><td style="padding: 15px 20px;">50%</td></tr>
            <tr><td style="padding: 15px 20px;">14</td><td style="padding: 15px 20px;">L.C.C work</td><td style="padding: 15px 20px;">100%</td></tr>
            <tr><td style="padding: 15px 20px;">15</td><td style="padding: 15px 20px;">Grill & Railing work</td><td style="padding: 15px 20px;">55%</td></tr>
            <tr><td style="padding: 15px 20px;">16</td><td style="padding: 15px 20px;">Inside paint & Polishing work</td><td style="padding: 15px 20px;">02%</td></tr>
            <tr><td style="padding: 15px 20px;">17</td><td style="padding: 15px 20px;">Switch -Socket fitting work</td><td style="padding: 15px 20px;">Not Yet Started</td></tr>
            <tr><td style="padding: 15px 20px;">18</td><td style="padding: 15px 20px;">Outside paint work</td><td style="padding: 15px 20px;">Not Yet Started</td></tr>
            <tr><td style="padding: 15px 20px;">19</td><td style="padding: 15px 20px;">Door shutter work</td><td style="padding: 15px 20px;">Not Yet Started</td></tr>
            <tr><td style="padding: 15px 20px;">20</td><td style="padding: 15px 20px;">Aluminum/Glazing work</td><td style="padding: 15px 20px;">60%</td></tr>
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
