@extends('layouts.app')

@section('title', 'Employees')

@push('styles')
<style>
    .swal2-container {
        z-index: 20000 !important;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

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
    }



    .delete-btn {
        background-color: #ef4444;
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

        <button class="btn-primary" id="addEmployeeBtn">
            <i class="fas fa-plus"></i> Add Employee
        </button>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Account type</th>
                <!-- <th>Status</th> -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="employeeTableBody">
            @forelse($employees as $emp)
            <tr data-href="{{ route('employees.panel', $emp->id) }}">
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->username }}</td>
                <td>{{ $emp->email }}</td>
                <td>{{ $emp->is_admin ? 'Admin' : 'Employee' }}</td>
                <!-- <td>
                        <span class="status-badge {{ $emp->is_active ? 'active' : 'inactive' }}">
                            {{ $emp->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td> -->
                <td>
                    <button class="btn-primary edit-btn"
                        data-id="{{ $emp->id }}"
                        data-name="{{ $emp->name }}"
                        data-email="{{ $emp->email }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action delete-btn" data-id="{{ $emp->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
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
                // Variables
                const modal = document.getElementById('employeeModal');
                const modalTitle = document.getElementById('modalTitle');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');
                const idInput = document.getElementById('emp_id');

                // Reset Password Elements
                const resetPassContainer = document.getElementById('resetPasswordContainer');
                const resetPassInput = document.getElementById('reset_password');

                // const rows = document.querySelectorAll('tr[data-href]');

                // rows.forEach(row => {
                //     row.addEventListener('click', () => {
                //         window.location.href = row.dataset.href; // <--- This redirects the page
                //     });
                // });

                // Search Logic
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('employeeTableBody');

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


                

                    // Modal Logic
                    const openModal = () => {
                        modal.style.display = 'flex';
                    };
                    const closeModal = () => {
                        modal.style.display = 'none';
                    };

                    // ADD BUTTON CLICK
                    document.getElementById('addEmployeeBtn').addEventListener('click', () => {
                        idInput.value = '';
                        nameInput.value = '';
                        emailInput.value = '';

                        // Hide reset password option (new users get default pass anyway)
                        resetPassContainer.style.display = 'none';
                        resetPassInput.checked = false;

                        modalTitle.innerText = "Add New Employee";
                        openModal();
                    });

                    document.querySelector('.close-modal-btn').addEventListener('click', closeModal);
                    document.querySelector('.btn-cancel').addEventListener('click', closeModal);

                    // TABLE ACTIONS
                    tableBody.addEventListener('click', (e) => {
                        const editBtn = e.target.closest('.edit-btn');
                        const deleteBtn = e.target.closest('.delete-btn');

                        // EDIT CLICK
                        if (editBtn) {
                            idInput.value = editBtn.dataset.id;
                            nameInput.value = editBtn.dataset.name;
                            emailInput.value = editBtn.dataset.email;

                            // Show reset password option
                            resetPassContainer.style.display = 'block';
                            resetPassInput.checked = false; // Always unchecked when opening

                            modalTitle.innerText = "Edit Employee";
                            openModal();
                        }

                        // DELETE CLICK
                        else if (deleteBtn) {
                            const id = deleteBtn.dataset.id;
                            Swal.fire({
                                title: 'Remove Employee?',
                                text: "They will no longer be able to log in.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Yes, remove'
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
                                                    text: result.message,
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

                        else {
                        const row = e.target.closest('tr[data-href]');
                        if (row) {
                            window.location.href = row.dataset.href;
                        }
                    }
                    });

                    // SAVE BUTTON CLICK
                    document.querySelector('.btn-save').addEventListener('click', async (e) => {
                        e.preventDefault();

                        const id = idInput.value;

                        const data = {
                            name: nameInput.value,
                            position: 'Employee', // Default role
                            email: emailInput.value,
                            // Send the reset flag
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