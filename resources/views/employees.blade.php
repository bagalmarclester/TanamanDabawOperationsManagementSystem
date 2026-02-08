@extends('layouts.app')

@section('title', 'Employees')

@push('styles')
<style>
    .swal2-container {
        z-index: 20000 !important;
    }

    /* Status Badges */
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Buttons */
    .btn-primary,
    .btn-action {
        border: none;
        cursor: pointer;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background-color: #319B72;
        color: white;
        padding: 8px 12px;
        gap: 8px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
        margin-right: 5px;
        color: white;
        transition: background 0.2s;
    }

    .edit-btn {
        background-color: #319B72;
    }

    .delete-btn {
        background-color: #ef4444;
    }

    /* Form Styles */
    .modal-form select,
    .modal-form input[type="text"],
    .modal-form input[type="email"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 5px;
        background-color: white;
    }

    .helper-text {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 4px;
    }

    .radio-group {
        display: flex;
        gap: 15px;
        margin-top: 5px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .radio-label input {
        width: auto;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>Employees</h2>
        <p>Manage team members and roles</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div style="position: relative;">
            <input type="text" id="searchInput" placeholder="Search employees..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </div>

        {{-- Only Admin can ADD employees --}}
        @if(auth()->user()->role === 'Admin')
            <button class="btn-primary" id="addEmployeeBtn">
                <i class="fas fa-plus"></i> Add Employee
            </button>
        @endif
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="employeeTableBody">
            @forelse($employees as $emp)
            <tr data-href="{{ route('employees.panel', $emp->id) }}">
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->email }}</td>
                <td>
                    <strong>{{ $emp->role }}</strong>
                </td>
                <td>
                    <span class="status-badge {{ $emp->status === 'Active' ? 'active' : 'inactive' }}">
                        {{ $emp->status }}
                    </span>
                </td>
                <td>
                    <div>
                        {{-- 
                            LOGIC: 
                            1. Admin can edit everyone.
                            2. Ops Manager can edit everyone EXCEPT 'Admin' and 'Operations Manager'
                        --}}
                        @if(auth()->user()->role === 'Admin' || ($emp->role !== 'Admin' && $emp->role !== 'Operations Manager'))
                            <button class="btn-action edit-btn"
                                data-id="{{ $emp->id }}"
                                data-name="{{ $emp->name }}"
                                data-email="{{ $emp->email }}"
                                data-role="{{ $emp->role }}"
                                data-status="{{ $emp->status }}"
                                title="Edit Details">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif

                        {{-- SECURITY UPDATE: Only Admin can FIRE (Delete) employees --}}
                        @if(auth()->user()->role === 'Admin')
                            <button class="btn-action delete-btn"
                                data-id="{{ $emp->id }}"
                                title="Delete Permanently">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">No employees found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="employeeModal" style="display: none;">
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

            <div class="input-group">
                <label>Role / Access Level</label>
                <select id="role" required>
                    <option value="" disabled selected>Select a Role</option>
                    
                    {{-- Only Admin can see/assign the Operations Manager role --}}
                    @if(auth()->user()->role === 'Admin')
                        <option value="Operations Manager">Operations Manager</option>
                    @endif

                    <option value="Head Landscaper">Head Landscaper</option>
                    <option value="Field Crew">Field Crew</option>
                </select>
                <p class="helper-text">* Field Crew members cannot log in to the dashboard.</p>
            </div>

            <div class="input-group" id="statusContainer" style="display: none; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ddd;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Account Status</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="status" value="Active" id="statusActive">
                        <span style="color: #065f46; font-weight: 600;">Active</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="Inactive" id="statusInactive">
                        <span style="color: #991b1b;">Inactive (Deactivated)</span>
                    </label>
                </div>
                <p class="helper-text">Inactive users cannot log in.</p>
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
        const modal = document.getElementById('employeeModal');
        const modalTitle = document.getElementById('modalTitle');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const roleInput = document.getElementById('role');
        const idInput = document.getElementById('emp_id');

        const statusContainer = document.getElementById('statusContainer');
        const statusActive = document.getElementById('statusActive');
        const statusInactive = document.getElementById('statusInactive');

        const resetPassContainer = document.getElementById('resetPasswordContainer');
        const resetPassInput = document.getElementById('reset_password');

        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('employeeTableBody');
        
        const addEmployeeBtn = document.getElementById('addEmployeeBtn');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                for (let i = 0; i < rows.length; i++) {
                    let textContent = rows[i].innerText.toLowerCase();
                    rows[i].style.display = textContent.includes(filter) ? "" : "none";
                }
            });
        }

        const closeModal = () => {
            modal.style.display = 'none';
        };
        document.querySelector('.close-modal-btn').addEventListener('click', closeModal);
        document.querySelector('.btn-cancel').addEventListener('click', closeModal);

        // --- ADD BUTTON ---
        if (addEmployeeBtn) {
            addEmployeeBtn.addEventListener('click', () => {
                idInput.value = '';
                nameInput.value = '';
                emailInput.value = '';
                roleInput.value = '';

                // Hide Status (Default is Active)
                statusContainer.style.display = 'none';
                statusActive.checked = true;

                // Hide Reset Password
                resetPassContainer.style.display = 'none';
                resetPassInput.checked = false;

                modalTitle.innerText = "Add New Employee";
                modal.style.display = 'flex';
            });
        }

        // --- TABLE ACTIONS ---
        tableBody.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');
            const row = e.target.closest('tr[data-href]');

            // EDIT CLICK
            if (editBtn) {
                e.stopPropagation();

                idInput.value = editBtn.dataset.id;
                nameInput.value = editBtn.dataset.name;
                emailInput.value = editBtn.dataset.email;
                roleInput.value = editBtn.dataset.role;

                // Show Status Radio Buttons
                statusContainer.style.display = 'block';

                // Set Correct Radio Button
                if (editBtn.dataset.status === 'Active') {
                    statusActive.checked = true;
                } else {
                    statusInactive.checked = true;
                }

                // Show Reset Password
                resetPassContainer.style.display = 'block';
                resetPassInput.checked = false;

                modalTitle.innerText = "Edit Employee";
                modal.style.display = 'flex';
            }

            // DELETE CLICK
            else if (deleteBtn) {
                e.stopPropagation();

                const id = deleteBtn.dataset.id;
                Swal.fire({
                    title: 'Delete Permanently?',
                    text: "This action cannot be undone.",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/employees/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            if (response.ok) {
                                Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Employee removed.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', 'Could not delete.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                        }
                    }
                });
            }

            // ROW CLICK
            else if (row) {
                window.location.href = row.dataset.href;
            }
        });

        // SAVE BUTTON
        document.querySelector('.btn-save').addEventListener('click', async (e) => {
            e.preventDefault();

            const id = idInput.value;
            const statusValue = document.querySelector('input[name="status"]:checked').value;

            const data = {
                name: nameInput.value,
                email: emailInput.value,
                role: roleInput.value,
                status: statusValue,
                reset_password: resetPassInput.checked
            };

            let url = "{{ route('employees.store') }}";
            let method = "POST";

            if (id) {
                url = `/employees/${id}`;
                method = "PUT";
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
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
    });
</script>
@endpush