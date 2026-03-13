@extends('layouts.app')

@section('title', 'Projects Details | Tanaman')

@push('styles')
<style>
    :root {
        --primary-green: #319B72;
        --primary-dark: #277c5b;
        --text-dark: #334155;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --bg-gray: #f8fafc;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .back-link {
        text-decoration: none;
        color: var(--text-muted);
        font-size: .875rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--primary-green);
    }

    .project-header-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }

    @media (min-width: 768px) {
        .project-header-section {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
        }
    }

    .header-title-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    @media (min-width: 640px) {
        .header-title-group {
            flex-direction: row;
            align-items: center;
            gap: 12px;
        }
    }

    .project-header-section h2 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--text-dark);
        font-weight: 700;
        line-height: 1.2;
    }

    @media (min-width: 768px) {
        .project-header-section h2 {
            font-size: 1.75rem;
        }
    }

    .project-header-section .sub-header {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: .95rem;
    }

    .header-badge {
        padding: 4px 12px;
        font-size: .75rem;
        border-radius: 20px;
        font-weight: 600;
        align-self: flex-start;
    }

    .header-badge.active { background: #dcfce7; color: #166534; }
    .header-badge.completed { background: var(--primary-green); color: #fff; }

    .tab-navigation {
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 0;
        margin-bottom: 20px;
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .tab-button {
        flex: 1;
        min-width: 120px;
        padding: 14px 20px;
        background: #fff;
        border: none;
        border-right: 1px solid var(--border-color);
        color: var(--text-muted);
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }

    .tab-button:last-child { border-right: none; }
    .tab-button:hover, .tab-button.active { background: #f8fafc; color: var(--primary-green); }
    .tab-button.active::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: var(--primary-green);
    }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    @media (min-width: 640px) {
        .info-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (min-width: 1024px) {
        .info-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .info-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 20px;
    }

    .info-card-full { grid-column: 1 / -1; }

    .info-card h3 { margin: 0 0 12px 0; font-size: 1rem; font-weight: 700; color: var(--text-dark); }
    
    .info-label {
        font-size: .65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;
        margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
    }
    .info-label i { color: var(--primary-green); font-size: .85rem; }
    .info-value { font-size: .95rem; color: var(--text-dark); font-weight: 500; line-height: 1.5; }

    #team.active {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    @media (min-width: 768px) {
        #team.active { flex-direction: row; }
    }

    .team-section {
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 20px;
        flex: 1;
    }

    .team-section h3 {
        margin: 0 0 16px 0; font-size: 1rem; font-weight: 700; color: var(--text-dark);
        display: flex; align-items: center; gap: 8px;
    }
    .team-section h3 i { color: var(--primary-green); }

    .crew-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .crew-badge {
        display: inline-flex; align-items: center; padding: 6px 12px; font-size: 0.8rem;
        background: #f1f5f9; border: 1px solid var(--border-color); border-radius: 6px;
        font-weight: 500; color: #475569;
    }

    .client-info p { margin: 8px 0; font-size: .9rem; color: var(--text-muted); }
    .client-info p strong { color: var(--text-dark); font-weight: 600; }

    .gallery-container {
        background: #fff; 
        border-radius: 12px; 
        border: 1px solid var(--border-color);
        overflow: hidden; 
    }

    .gallery-header {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        background: #fff;
    }

    .gallery-title {
        display: flex; 
        align-items: center; 
        gap: 10px; 
        font-size: .95rem;
        font-weight: 700; 
        color: var(--text-dark); 
    }
    
    .gallery-title i { color: var(--primary-green); font-size: 1.1rem; }

    .btn-add-mini {
        background-color: var(--primary-green);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }
    .btn-add-mini:hover { background-color: var(--primary-dark); }
    .btn-add-mini i { font-size: 0.8rem; }

    .gallery-content {
        padding: 20px;
        background: #fff;
        min-height: 200px;
    }
    
    .gallery-content.empty {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 40px 20px; background: #f8fafc;
    }

    .gallery-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 8px;
    }

    @media (max-width: 480px) {
        .gallery-list { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    }

    @media (min-width: 768px) {
        .gallery-list { grid-template-columns: repeat(4, 1fr); gap: 12px; }
    }

    @media (min-width: 1024px) {
        .gallery-list { grid-template-columns: repeat(5, 1fr); gap: 16px; }
    }

    .gallery-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        background: #f1f5f9;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }

    .gallery-item:hover { transform: scale(1.03); }

    .gallery-item img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        opacity: 0; transition: opacity 0.3s ease-in;
    }
    .gallery-item img.loaded { opacity: 1; }

    .gallery-item-overlay {
        position: absolute;
        bottom: 0; left: 0; width: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
        padding: 20px 8px 6px;
        color: white;
        font-size: 0.7rem;
        font-weight: 500;
        text-align: right;
        pointer-events: none;
    }

    .gallery-skeleton {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        z-index: 1;
    }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    .gallery-placeholder i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px; }
    .gallery-placeholder p { font-size: 0.95rem; color: var(--text-muted); margin: 0; }

    .btn-action {
        padding: 10px 20px; font-size: .875rem; border-radius: 6px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center;
        gap: 8px; border: none; justify-content: center; width: 100%;
    }

    @media (min-width: 640px) {
        .btn-action { width: auto; }
    }

    .btn-upload-photo {
        background: var(--primary-green); color: #fff;
    }
    .btn-upload-photo:hover { background: var(--primary-dark); }
    
    .btn-complete {
        background: #fff; border: 2px solid var(--primary-green); color: var(--primary-green);
    }
    .btn-complete:hover { background: #f0fdf4; }

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-container { background: #fff; width: 95%; max-width: 500px; border-radius: 12px; overflow: hidden; animation: modalSlideIn 0.3s; margin: 10px; display: flex; flex-direction: column; max-height: 90vh; }
    @keyframes modalSlideIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 1.1rem; color: var(--text-dark); }
    .modal-close { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.5rem; }
    .modal-body { padding: 20px; overflow-y: auto; }
    .upload-area { border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px; text-align: center; background: #f8fafc; cursor: pointer; }
    .preview-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 16px; }
    .preview-item { position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 1; border: 1px solid #e2e8f0; }
    .preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .remove-preview { position: absolute; top: 4px; right: 4px; background: rgba(255,255,255,0.9); color: #ef4444; border: none; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .modal-footer { padding: 16px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; }
    .btn-cancel { padding: 8px 16px; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; color: var(--text-muted); cursor: pointer; }
    .btn-action.btn-upload-photo { width: auto; }
    
    .scroll-to-top { position: fixed; bottom: 20px; right: 20px; background: var(--primary-green); color: white; width: 42px; height: 42px; border-radius: 50%; border: none; cursor: pointer; display: none; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(49,155,114,0.3); z-index: 900; }
    .scroll-to-top.show { display: flex; }

    /* Lightbox Styles */
    .image-modal-overlay {
        position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.95); display: none;
        justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease;
    }
    .image-modal-overlay.active { display: flex; opacity: 1; }
    
    .image-modal-content {
        max-width: 90%; max-height: 90vh;
        object-fit: contain; border-radius: 4px; 
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
        animation: zoomIn 0.3s ease;
    }
    @keyframes zoomIn { from {transform: scale(0.9); opacity: 0;} to {transform: scale(1); opacity: 1;} }

    .image-modal-close {
        position: absolute; top: 20px; right: 30px; color: #f1f1f1;
        font-size: 40px; font-weight: 300; cursor: pointer; transition: color 0.2s; z-index: 2001;
    }
    .image-modal-close:hover { color: #fff; }
</style>
@endpush

@section('content')

@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ title: 'Success!', text: "{{ session('success') }}", icon: 'success', timer: 2000, showConfirmButton: false });
    });
</script>
@endif

<div class="page-header">
    <div style="width: 100%;">
        <a href="{{ route('projects') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Projects</a>
        <div class="project-header-section">
            <div class="header-title-group">
                <div>
                    <h2>{{ $project->project_name }}</h2>
                    <p class="sub-header">{{ $project->client->name }}</p>
                </div>
                <div>
                    <span class="header-badge {{ $project->is_active ? 'active' : 'completed' }}">
                        {{ $project->is_active ? 'Active' : 'Completed' }}
                    </span>
                </div>
            </div>
            
            <div style="margin-top: 10px; width: 100%; display: flex; justify-content: flex-end;">
                @if($project->is_active && in_array(auth()->user()->role, ['Admin', 'Operations Manager']))
                    <button type="button" id="markCompletedBtn" class="btn-action btn-complete">
                        <i class="fas fa-check"></i> Mark as Completed
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="tab-navigation">
    <button class="tab-button active" data-tab="project-info"><i class="fas fa-info-circle"></i> Project Info</button>
    <button class="tab-button" data-tab="gallery"><i class="fas fa-images"></i> Gallery</button>
    <button class="tab-button" data-tab="team"><i class="fas fa-users"></i> Team & Client</button>
</div>

<div class="tab-content active" id="project-info">
    <div class="info-grid">
        @if($project->project_description)
        <div class="info-card info-card-full">
            <h3>Project Description</h3>
            <p class="info-value">{{ $project->project_description }}</p>
        </div>
        @endif
        <div class="info-card">
            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Location</div>
            <div class="info-value">{{ $project->project_location }}</div>
        </div>
        <div class="info-card">
            <div class="info-label"><i class="fas fa-dollar-sign"></i> Budget</div>
            <div class="info-value">₱{{ number_format($project->project_budget, 2) }}</div>
        </div>
        <div class="info-card">
            <div class="info-label"><i class="fas fa-calendar-check"></i> Start Date</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($project->created_at)->format('M d, Y') }}</div>
        </div>
        <div class="info-card">
            <div class="info-label"><i class="fas fa-calendar-times"></i> Due Date</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($project->project_end_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

<div class="tab-content" id="gallery">
    <div class="gallery-container">
        <div class="gallery-header">
            <div class="gallery-title">
                <i class="fas fa-images"></i>
                <span>Progression Gallery</span>
            </div>
            
            @if($project->is_active && in_array(auth()->user()->role, ['Admin', 'Operations Manager', 'Head Landscaper']))
                <button type="button" class="btn-add-mini" id="openUploadModalBtn">
                    <i class="fas fa-plus"></i> Add Photos
                </button>
            @endif
        </div>

        <div class="gallery-content {{ count($signedImages) === 0 ? 'empty' : '' }}" id="galleryViewport">
            @if(count($signedImages) > 0)
            <div class="gallery-list">
                @foreach($signedImages as $index => $imageUrl)
                <div class="gallery-item" onclick="openLightbox('{{ $imageUrl }}')">
                    <div class="gallery-skeleton"></div>
                    <img src="{{ $imageUrl }}" class="gallery-list-image" loading="lazy">
                    <div class="gallery-item-overlay">
                        {{ now()->format('M d') }}
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="gallery-placeholder">
                <i class="far fa-image"></i>
                <p>No photos yet</p>
            </div>
            @endif
        </div>
    </div>
</div>

@if(in_array(auth()->user()->role, ['Admin', 'Operations Manager', 'Head Landscaper']))
<div class="modal-overlay" id="uploadModal">
    <div class="modal-container">
        <form action="{{ route('projects.upload', $project->id) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="modal-header">
                <h3>Upload Photos</h3>
                <button type="button" class="modal-close" id="closeModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <input type="file" name="progression_images[]" id="modalFileInput" multiple accept="image/*" style="display: none;">
                <div class="upload-area" id="dropZone">
                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                    <p style="margin:0; font-weight:600; color:#475569;">Click to Upload</p>
                    <p style="margin:4px 0 0; font-size:0.8rem; color:#94a3b8;">Max 5MB per image</p>
                </div>
                <div class="preview-grid" id="previewContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelModalBtn">Cancel</button>
                <button type="submit" class="btn-add-mini" id="submitUploadBtn" disabled style="padding: 8px 16px; font-size: 0.9rem;">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="image-modal-overlay" id="imageLightbox">
    <span class="image-modal-close" onclick="closeLightbox()">&times;</span>
    <img class="image-modal-content" id="expandedImg">
</div>

<div class="tab-content" id="team">
    <div class="team-section">
        <h3><i class="fas fa-hard-hat"></i> Team Assignment</h3>
        <div class="info-card" style="border:none; padding:0; box-shadow:none;">
            <div class="info-label">Head Landscaper</div>
            <div class="info-value" style="margin-bottom:12px;">{{ $project->headLandscaper->name ?? 'Unassigned' }}</div>
            <div class="info-label">Field Crew</div>
            <div class="crew-list">
                @forelse($project->fieldCrew as $member)
                <span class="crew-badge">{{ $member->name }}</span>
                @empty
                <span style="color:#94a3b8; font-style:italic; font-size:0.9em;">No crew assigned</span>
                @endforelse
            </div>
        </div>
    </div>
    <div class="team-section">
        <h3><i class="fas fa-address-card"></i> Client Contact</h3>
        <div class="client-info">
            <p><strong>Name:</strong> {{ $project->client->name }}</p>
            <p><strong>Email:</strong> {{ $project->client->email }}</p>
            @if($project->client->phone) <p><strong>Phone:</strong> {{ $project->client->phone }}</p> @endif
        </div>
    </div>
</div>

<button class="scroll-to-top" id="scrollToTopBtn"><i class="fas fa-arrow-up"></i></button>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.dataset.tab;
                tabButtons.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });

        const imgs = document.querySelectorAll('.gallery-list-image');
        imgs.forEach(img => {
            const load = () => { img.classList.add('loaded'); img.previousElementSibling.style.display='none'; };
            if(img.complete) load(); else img.addEventListener('load', load);
        });

        const scrollBtn = document.getElementById('scrollToTopBtn');
        window.addEventListener('scroll', () => {
            if(window.scrollY > 300) scrollBtn.classList.add('show'); else scrollBtn.classList.remove('show');
        });
        scrollBtn.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));

        const modal = document.getElementById('uploadModal');
        if (modal) {
            const fileInput = document.getElementById('modalFileInput');
            const dropZone = document.getElementById('dropZone');
            const previewContainer = document.getElementById('previewContainer');
            const submitBtn = document.getElementById('submitUploadBtn');
            let fileDataTransfer = new DataTransfer();

            const openModal = () => modal.classList.add('active');
            const closeModal = () => modal.classList.remove('active');

            document.getElementById('openUploadModalBtn')?.addEventListener('click', openModal);
            document.getElementById('closeModalBtn')?.addEventListener('click', closeModal);
            document.getElementById('cancelModalBtn')?.addEventListener('click', closeModal);
            modal.addEventListener('click', e => { if(e.target===modal) closeModal(); });

            if(dropZone) dropZone.addEventListener('click', () => fileInput.click());
            if(fileInput) fileInput.addEventListener('change', e => processFiles(e.target.files));

            function processFiles(files) {
                for (let i=0; i<files.length; i++) {
                    if (files[i].type.startsWith('image/')) fileDataTransfer.items.add(files[i]);
                }
                fileInput.files = fileDataTransfer.files;
                renderPreviews();
                submitBtn.disabled = fileInput.files.length === 0;
            }

            function renderPreviews() {
                previewContainer.innerHTML = '';
                if(fileDataTransfer.files.length > 0) {
                    dropZone.style.display = 'none';
                    previewContainer.style.display = 'grid';
                    Array.from(fileDataTransfer.files).forEach((file, index) => {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        const btn = document.createElement('div');
                        btn.className = 'remove-preview';
                        btn.innerHTML = '<i class="fas fa-times"></i>';
                        btn.onclick = (e) => { e.stopPropagation(); removeFile(index); };
                        div.append(img, btn);
                        previewContainer.append(div);
                    });
                    const addBtn = document.createElement('div');
                    addBtn.className = 'add-more-btn';
                    addBtn.innerHTML = '<i class="fas fa-plus"></i>';
                    addBtn.onclick = (e) => { e.stopPropagation(); fileInput.click(); };
                    previewContainer.append(addBtn);
                } else {
                    dropZone.style.display = 'block';
                    previewContainer.style.display = 'none';
                }
            }

            function removeFile(index) {
                const newDt = new DataTransfer();
                Array.from(fileDataTransfer.files).forEach((f, i) => { if(i!==index) newDt.items.add(f); });
                fileDataTransfer = newDt;
                fileInput.files = fileDataTransfer.files;
                renderPreviews();
                submitBtn.disabled = fileInput.files.length === 0;
            }
        }

        const compBtn = document.getElementById('markCompletedBtn');
        if(compBtn) {
            compBtn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Complete Project?', icon: 'question', showCancelButton: true,
                    confirmButtonText: 'Yes', confirmButtonColor: '#319B72'
                }).then(res => {
                    if(res.isConfirmed) {
                        fetch(`/projects/{{ $project->id }}/complete`, {
                            method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                        }).then(r => r.json()).then(d => {
                            Swal.fire('Error!', d.message, 'error').then(() => window.location.reload());
                        });
                    }
                });
            });
        }
    });

    const lightbox = document.getElementById('imageLightbox');
    const expandedImg = document.getElementById('expandedImg');

    function openLightbox(src) {
        expandedImg.src = src;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox || e.target.classList.contains('image-modal-close')) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            closeLightbox();
        }
    });
</script>
@endpush