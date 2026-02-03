@extends('layouts.app')

@section('title', 'Projects Details | Tanaman')

@push('styles')

<style>
    .gallery-viewport {
        width: 100%;
        height: 350px;
        background-color: #f3f4f6;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        margin-bottom: 15px;
    }

    .gallery-viewport img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .drop-zone-static {
        border: 2px dashed #cbd5e1;
        background-color: #f8fafc;
        height: 300px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #64748b;
    }

    .gallery-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .gallery-footer {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .hidden {
        display: none !important;
    }
</style>

@endpush

@section('content')



@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    });
</script>
@endif

<div class="page-header">
    <div>
        <a href="{{ route('projects') }}" style="text-decoration:none; color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 5px; margin-bottom: 10px;">
            <i class="fas fa-arrow-left"></i> Back to Projects
        </a>
        <div class="project-title-section">
            <div class="project-title-left">
                <h2 id="displayName">{{ $project->project_name }}</h2>
                <p class="sub-header">{{ $project->client->name }}</p>
            </div>
            <div class="project-title-right">
                <span class="header-badge active">Active</span>
            </div>
        </div>
    </div>
</div>


<div class="project-detail-container">
    <aside class="project-detail-left">
        <div class="info-card project-info">
            <div class="card-title">
                <i class="fas fa-info-circle"></i>
                <h3>PROJECT INFO</h3>
            </div>
            <div class="card-content">
                <!-- <div class="info-section">
                    <label>DESCRIPTION</label>
                    <p>This is the description area</p>
                </div> -->
                <div class="info-grid">
                    <div class="info-item">
                        <label>BUDGET</label>
                        <p id="projectBudget">₱{{ $project->project_budget }}</p>
                    </div>
                    <div class="info-item">
                        <label>End Date</label>
                        <p id="projectEndDate">{{ $project->project_end_date }}</p>
                    </div>
                </div>
                <div class="info-item">
                    <label>Created</label>
                    <p id="projectCreated">{{ $project->project_start_date }}</p>
                </div>
            </div>
        </div>
        <div class="info-card client-contact">
            <div class="card-title">
                <h3>Client Contact</h3>
            </div>
            <div class="card-content">
                <p class="client-name" id="clientName">{{ $project->client->name }}</p>
                <p class="client-email" id="clientEmail">{{ $project->client->email }}</p>
                <p class="client-phone" id="clientPhone">{{ $project->client->phone }}</p>
            </div>
        </div>
    </aside>
    



<div class="stat-card" style="display: block;">

    <form action="{{ route('projects.upload', $project->id) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        <div class="gallery-header">
            <h3 class="panel-section-title">
                <i class="fas fa-camera-retro" style="color: #319B72; margin-right:5px;"></i>
                Progression Gallery
            </h3>

            <button type="button" class="btn-primary" id="triggerUploadBtn">
                <i class="fas fa-cloud-upload-alt"></i> Upload Photo
            </button>

            <input type="file" name="progression_images[]" id="fileInput" multiple style="display: none;">
        </div>

        @if(count($signedImages) > 0)
        <div class="gallery-viewport" id="galleryViewport" data-images="{{ json_encode($signedImages) }}">

            <img src="{{ $signedImages[0] }}" alt="Project Image" id="mainGalleryImage">

            @if(count($signedImages) > 1)
            <div style="position: absolute; top: 50%; width: 100%; display: flex; justify-content: space-between; padding: 0 10px; transform: translateY(-50%);">

                <button type="button" id="prevBtn" style="background: rgba(0,0,0,0.5); border: none; color: white; padding: 10px; border-radius: 50%; cursor: pointer; z-index: 10;">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button type="button" id="nextBtn" style="background: rgba(0,0,0,0.5); border: none; color: white; padding: 10px; border-radius: 50%; cursor: pointer; z-index: 10;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            @endif

            <div id="previewMsg" class="hidden" style="position: absolute; inset:0; background:white; display:flex; align-items:center; justify-content:center; color:#666;">
                Preview not available. Click "Save Photos" to upload.
            </div>
        </div>
        <div id="imageCounter" style="text-align: center; color: #6b7280; font-size: 0.9rem; margin-bottom: 15px;">
            Image 1 of {{ count($signedImages) }}
        </div>

        @else
        <div class="drop-zone-static" id="dropZone">
            <i class="far fa-image" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
            <p style="font-weight: 600; color: #475569; margin-bottom: 5px;">No progression photos uploaded yet</p>
            <p style="font-size: 0.9rem;">Click "Upload Photo" above</p>
        </div>
        @endif

        <div id="stagedFileMsg" class="hidden" style="margin-top: 15px; padding: 10px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 6px; text-align: center;">
            <i class="fas fa-check-circle"></i> <span id="fileCount">0</span> file(s) selected. Click "Save Photos" to confirm.
        </div>

        <div class="gallery-footer">
            <a href="#" class="btn-cancel" style="text-decoration: none; padding: 10px 15px; color: #64748b;">Back</a>
            <!-- 
                            <button type="button" class="btn-primary" style="background-color: #3b82f6;">
                                <i class="fas fa-check"></i> Approved
                            </button> -->

            <button type="submit" id="saveBtn" class="btn-primary" disabled style="opacity: 0.6; cursor: not-allowed;">
                <i class="fas fa-save"></i> Save Photos
            </button>
        </div>
    </form>

</div>

</div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. Navbar Logic ---
        const profileTrigger = document.getElementById('profile-trigger');
        const dropdownMenu = document.getElementById('profile-dropdown');
        if (profileTrigger) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            window.addEventListener('click', function(e) {
                if (!dropdownMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }

        // --- 2. Carousel Logic (No Red Lines) ---
        const mainImage = document.getElementById('mainGalleryImage');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const counter = document.getElementById('imageCounter');
        const viewport = document.getElementById('galleryViewport');

        let galleryImages = [];
        let currentIndex = 0;

        // Load data from HTML attribute safely
        if (viewport && viewport.dataset.images) {
            try {
                galleryImages = JSON.parse(viewport.dataset.images);
            } catch (e) {
                console.error("Error parsing gallery images:", e);
            }
        }

        // Only initialize if we have images and buttons
        if (galleryImages.length > 1 && mainImage && prevBtn && nextBtn) {

            function updateGallery() {
                mainImage.src = galleryImages[currentIndex];
                if (counter) {
                    counter.innerText = `Image ${currentIndex + 1} of ${galleryImages.length}`;
                }
            }

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                currentIndex++;
                if (currentIndex >= galleryImages.length) {
                    currentIndex = 0;
                }
                updateGallery();
            });

            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                currentIndex--;
                if (currentIndex < 0) {
                    currentIndex = galleryImages.length - 1;
                }
                updateGallery();
            });
        }

        // --- 3. Upload Logic ---
        const fileInput = document.getElementById('fileInput');
        const triggerBtn = document.getElementById('triggerUploadBtn');
        const saveBtn = document.getElementById('saveBtn');
        const stagedMsg = document.getElementById('stagedFileMsg');
        const fileCountSpan = document.getElementById('fileCount');
        const dropZone = document.getElementById('dropZone');
        const previewMsg = document.getElementById('previewMsg');

        if (triggerBtn) {
            triggerBtn.addEventListener('click', () => {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const files = this.files;
                if (files.length > 0) {
                    fileCountSpan.innerText = files.length;
                    stagedMsg.classList.remove('hidden');
                    saveBtn.disabled = false;
                    saveBtn.style.opacity = '1';
                    saveBtn.style.cursor = 'pointer';
                    if (dropZone) {
                        dropZone.innerHTML = '<i class="fas fa-check" style="font-size: 3rem; color: #319B72; margin-bottom: 15px;"></i><p style="font-weight:600; color:#166534;">Files Selected</p>';
                    }
                    if (previewMsg) {
                        previewMsg.classList.remove('hidden');
                    }
                } else {
                    stagedMsg.classList.add('hidden');
                    saveBtn.disabled = true;
                    saveBtn.style.opacity = '0.6';
                    saveBtn.style.cursor = 'not-allowed';
                    if (previewMsg) previewMsg.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush