@extends('layouts.app')

@section('title', 'Employees')

@push('styles')
<style>
    /* Ensure SweetAlert is on top */
    .swal2-container {
        z-index: 20000 !important;
    }

    /* Profile Grid Layout */
    .employee-profile-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 24px;
        margin-top: 20px;
    }

    /* Card Styling */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .card-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 24px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Info Groups */
    .info-group {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-icon-circle {
        width: 48px;
        height: 48px;
        background-color: #f8fafc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #319B72;
        font-size: 1.2rem;
    }

    .info-content {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #0f172a;
    }

    .text-green {
        color: #319B72;
    }

    /* Buttons */
    .btn-cancel,
    .btn-archive {
        border: 1px solid #ddd;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .btn-archive {
        background: #fee2e2;
        color: #991b1b;
        border: none;
    }

    .btn-archive:hover {
        background: #fecaca;
    }

    /* Badges */
    .header-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-active {
        background-color: #d1fae5;
        color: #065f46;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('employees') }}" style="text-decoration:none; color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 5px; margin-bottom: 10px;">
            <i class="fas fa-arrow-left"></i> Back to Employees
        </a>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 style="margin:0;">{{ $employee->name }}</h2>
            <span class="header-badge badge-active">Active</span>
        </div>
        <!-- <p style="margin-top: 5px; color: #64748b;"> Software Engineer • IT Department</p> -->
    </div>
    <div style="display: flex; gap: 10px;">
        <button id='editProfileBtn' class="btn-cancel" style="background: white;"
            data-id="{{ $employee->id }}"
            data-name="{{ $employee->name }}"
            data-email="{{ $employee->email }}">
            <i class="fas fa-pen" style="margin-right: 5px;"></i> Edit Profile
        </button>

        <button id='deleteEmployeeBtn' class="btn-archive" data-id="{{ $employee->id }}">
            <i class="far fa-trash-alt"></i> Delete
        </button>
    </div>
</div>

<div class="employee-profile-grid">

    <div class="stat-card" style="display: block;">
        <div class="card-title-row">
            <i class="far fa-user"></i>
            <span>Personal Info</span>
        </div>

        <div class="info-group">
            <div class="info-icon-circle">
                <i class="far fa-user"></i>
            </div>
            <div class="info-content">
                <label class="info-label">Full Name</label>
                <div id="full-name" class="info-value">{{ $employee->name }}</div>
            </div>
        </div>

        <div class="info-group">
            <div class="info-icon-circle">
                <i class="far fa-envelope"></i>
            </div>
            <div class="info-content">
                <label class="info-label">Email Address</label>
                <div class="text-green">{{ $employee->email }}</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="display: block;">
        <div class="card-title-row">
            <i class="fas fa-shield-alt"></i>
            <span>Role & Status</span>
        </div>

        <div class="role-grid">
            <div class="input-group">
                <label class="info-label">Department</label>
                <input type="text" value="Engineering" readonly style="background-color: #f8fafc;">
            </div>

            <div class="input-group">
                <label class="info-label">Position</label>
                <input type="text" value="Senior Developer" readonly style="background-color: #f8fafc;">
            </div>
        </div>

        <div class="input-group" style="margin-top: 20px;">
            <label class="info-label">Date Joined</label>
            <div class="input-with-icon">
                <!-- <i class="far fa-calendar-alt"></i> -->
                <input type="text" value="{{ $employee->created_at->format('M d, Y') }}" readonly style="background-color: #f8fafc;">
            </div>
        </div>

        <div class="input-group" style="margin-top: 20px;">
            <label class="info-label">Employee ID</label>
            <div style="color: #64748b;">{{ sprintf('#EMP-%03d', $employee->id) }}</div>
        </div>
    </div>

</div>


<div class="modal-overlay" id="employeeModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Employee</h3>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form class="modal-form">
            <input type="hidden" id="emp_id">

            <div class="input-group">
                <label>Employee Name</label>
                <input type="text" id="name" placeholder="e.g. John Doe" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="email" placeholder="contact@company.com" required>
            </div>
            <div class="input-group" id="resetPasswordContainer" style="display: none; margin-top: 15px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" id="reset_password" style="width: auto;">
                    <span style="font-size: 0.9rem;">Reset Password to "password123"</span>
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Employee</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log("Employee Profile Script Loaded"); // Debug check

        // --- 1. VARIABLES ---
        const modal = document.getElementById('employeeModal');
        const modalTitle = document.getElementById('modalTitle');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Form Inputs
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const idInput = document.getElementById('emp_id');
        const resetPassContainer = document.getElementById('resetPasswordContainer');
        const resetPassInput = document.getElementById('reset_password');

        // Display Elements
        const headerName = document.getElementById('headerName');
        const displayName = document.getElementById('display-name');
        const displayEmail = document.getElementById('display-email');

        // Buttons
        const editBtn = document.getElementById('editProfileBtn');
        const deleteBtn = document.getElementById('deleteEmployeeBtn');
        const saveBtn = document.querySelector('.btn-save');
        
        // Modal Close Buttons
        const closeModalBtn = document.querySelector('.close-modal-btn');
        // MAKE SURE your HTML Cancel button has class 'btn-cancel-modal'
        const cancelModalBtn = document.querySelector('.btn-cancel-modal'); 

        // --- 2. MODAL HELPERS ---
        const openModal = () => { 
            if(modal) modal.style.display = 'flex'; 
        };
        const closeModal = () => { 
            if(modal) modal.style.display = 'none'; 
        };

        // --- 3. EVENT LISTENERS (With Safety Checks) ---

        // Close Handlers
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
        window.onclick = (e) => { if (e.target === modal) closeModal(); };

        // EDIT BUTTON LOGIC
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                console.log("Edit Button Clicked");
                // Populate fields
                if(idInput) idInput.value = editBtn.dataset.id;
                if(nameInput) nameInput.value = editBtn.dataset.name;
                if(emailInput) emailInput.value = editBtn.dataset.email;

                // Reset Password Logic
                if(resetPassContainer) resetPassContainer.style.display = 'block';
                if(resetPassInput) resetPassInput.checked = false;

                if(modalTitle) modalTitle.innerText = "Edit Employee";
                openModal();
            });
        } else {
            console.error("Edit Button (id='editProfileBtn') not found!");
        }

        // SAVE BUTTON LOGIC
        if (saveBtn) {
            saveBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = idInput.value;

                const data = {
                    name: nameInput.value,
                    position: 'Employee',
                    email: emailInput.value,
                    reset_password: resetPassInput ? resetPassInput.checked : false
                };

                try {
                    const response = await fetch(`/employees/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Update UI
                        if(headerName) headerName.innerText = data.name;
                        if(displayName) displayName.innerText = data.name;
                        if(displayEmail) displayEmail.innerText = data.email;

                        // Update Button Data
                        if(editBtn) {
                            editBtn.dataset.name = data.name;
                            editBtn.dataset.email = data.email;
                        }

                        closeModal();

                        Swal.fire({
                            title: 'Success',
                            text: result.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', result.message || 'Validation failed', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'System error occurred', 'error');
                }
            });
        }

        // DELETE BUTTON LOGIC
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                const id = deleteBtn.dataset.id;

                Swal.fire({
                    title: 'Remove Employee?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/employees/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': csrfToken }
                            });

                            if (response.ok) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Employee removed. Redirecting...',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('employees') }}";
                                });
                            } else {
                                Swal.fire('Error', 'Could not delete.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire('Error', 'System error occurred', 'error');
                        }
                    }
                });
            });
        }
    });
</script>
@endpush