@extends('layouts.app') 

@section('content')
@php
    // Grab the first part of the URL (e.g., 'clients' from /clients/2001)
    $subsystem = request()->segment(1); 
@endphp
@push('styles')
<style>
    .btn-text {
        padding: 0.75rem 1rem;
        background-color: #1A4D3E;
        color:#fff;
        border-radius: 0.25rem;
    }
</style>
@endpush
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    
    <div class="card shadow-sm border-0" style="max-width: 500px; width: 100%; border-radius: 12px;">
        <div class="card-body text-center p-5">
            
            <!-- <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="#1b5e20" class="bi bi-tree-fill" viewBox="0 0 16 16">
                    <path d="M8.416.223a.5.5 0 0 0-.832 0l-3 4.5A.5.5 0 0 0 5 5.5h.098L3.076 8.739A.5.5 0 0 0 3.5 9.5h.191l-1.638 3.276a.5.5 0 0 0 .447.724H7V16h2v-2.5h4.5a.5.5 0 0 0 .447-.724L12.31 9.5h.191a.5.5 0 0 0 .424-.761L10.902 5.5H11a.5.5 0 0 0 .416-.777l-3-4.5z"/>
                </svg>
            </div>
             -->
            <!-- <h1 class="display-3 fw-bolder mb-2" style="color: #1b5e20;">404</h1> -->
            <h1 class="h4 fw-bold text-dark mb-3">Record Not Found</h1>
            <p class="text-muted mb-4" style="margin: 0.75rem 0rem 2rem 0rem;">
                We searched everywhere, but the specific record you are looking for doesn't exist or has been removed from the system.
            </p>

            <div class="d-inline-block">
                @if($subsystem)
                    
                    <a href="/{{ $subsystem }}" class="btn-text text-white px-4 py-2" style="text-decoration: none;">
                        ← Return to {{ Str::title($subsystem) }}
                    </a>
                    
                @else
                    <a href="/" class="btn-text text-white px-4 py-2" style="text-decoration: none;">
                        ← Return to Dashboard
                    </a>
                @endif
            </div>
            
        </div>
    </div>
</div>
@endsection
