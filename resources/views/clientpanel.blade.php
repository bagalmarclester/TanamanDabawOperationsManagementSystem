@extends('layouts.app')

@section('title', 'Client Profile | Tanaman')

@push('styles')
    <style>
        /* Small fix to ensure the active/inactive badges look right with dynamic data */
        .status-badge.active { background-color: #e6f4ea; color: #1e7e34; }
        .status-badge.inactive { background-color: #fce8e6; color: #c62828; }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <a href="{{ route('clients') }}" style="text-decoration:none; color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 5px; margin-bottom: 10px;">
                <i class="fas fa-arrow-left"></i> Back to Clients
            </a>
            <h2 id="displayName">{{ $client->name }}</h2>
            <p>Client ID: {{ sprintf('#CLI-%03d', $client->id) }}</p>
        </div>
        <div>
            <button class="btn-primary" id="openEditBtn">
                <i class="fas fa-pen"></i> Edit Profile
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid" style="grid-template-columns: 1fr 2fr;">
        
        {{-- Contact Info Card --}}
        <div class="stat-card" style="display: block;">
            <h3 class="panel-section-title">Contact Info</h3>
            
            <div class="info-group">
                <label class="info-label">Email</label>
                <div class="info-value" id="displayEmail">{{ $client->email }}</div>
            </div>
            
            <div class="info-group">
                <label class="info-label">Phone</label>
                <div class="info-value" id="displayPhone">{{ $client->phone ?? 'N/A' }}</div>
            </div>

            <div class="info-group">
                <label class="info-label">Address</label>
                <div class="info-value" id="displayAddress">{{ $client->address ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- Active Projects Card --}}
        <div class="stat-card" style="display: block;">
            <h3 class="panel-section-title">Active Projects</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->projects as $project)
                    <tr>
                        <td>{{ $project->project_name }}</td>
                        <td>
                            <span class="status-badge {{ $project->is_active ? 'active' : 'inactive' }}">
                                {{ $project->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($project->project_end_date)->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:#888;">No projects found for this client.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Edit Modal --}}
    <div class="modal-overlay" id="editProfileModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Client Profile</h3>
                <span class="close-modal-btn" id="closeEditBtn">&times;</span>
            </div>
            
            <form class="modal-form" id="editProfileForm">
                <div class="input-group">
                    <label>Client Name</label>
                    <input type="text" id="inputName" required>
                </div>
                
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" id="inputEmail" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="tel" id="inputPhone">
                </div>

                <div class="input-group">
                    <label>Address</label>
                    <input type="text" id="inputAddress">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="cancelEditBtn">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // --- 1. Pass Data from Blade to JS ---
        const clientId = <?php echo $client->id; ?>;

        // --- 2. Edit Profile Logic ---
        const modal = document.getElementById('editProfileModal');
        const openBtn = document.getElementById('openEditBtn');
        const closeBtn = document.getElementById('closeEditBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        const form = document.getElementById('editProfileForm');
        
        // Display Elements
        const displayName = document.getElementById('displayName');
        const displayEmail = document.getElementById('displayEmail');
        const displayPhone = document.getElementById('displayPhone');
        const displayAddress = document.getElementById('displayAddress');
        
        // Input Elements
        const inputName = document.getElementById('inputName');
        const inputEmail = document.getElementById('inputEmail');
        const inputPhone = document.getElementById('inputPhone');
        const inputAddress = document.getElementById('inputAddress');

        // Open Modal & Pre-fill Data
        if(openBtn) {
            openBtn.addEventListener('click', () => {
                inputName.value = displayName.innerText;
                inputEmail.value = displayEmail.innerText;
                inputPhone.value = displayPhone.innerText;
                inputAddress.value = displayAddress.innerText;
                modal.style.display = 'flex';
            });
        }

        // Close Modal Helper
        function closeModal() { modal.style.display = 'none'; }
        if(closeBtn) closeBtn.addEventListener('click', closeModal);
        if(cancelBtn) cancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        // Submit Logic (AJAX)
        if(form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault(); 
                
                const formData = {
                    name: inputName.value,
                    email: inputEmail.value,
                    phone: inputPhone.value,
                    address: inputAddress.value
                };

                try {
                    const response = await fetch(`/clients/${clientId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Update UI immediately on success
                        displayName.innerText = formData.name;
                        displayEmail.innerText = formData.email;
                        displayPhone.innerText = formData.phone;
                        displayAddress.innerText = formData.address;
                        
                        closeModal();

                        Swal.fire({
                            title: 'Profile Updated!',
                            text: 'Client information has been saved successfully.',
                            icon: 'success',
                            confirmButtonColor: '#319B72',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: result.message || 'Failed to update profile.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        title: 'System Error',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
        
    </script>
@endpush