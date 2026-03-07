@extends('layouts.app')

@section('title', 'Clients | Tanaman')

@section('content')

<style>
    .swal2-container {
        z-index: 20000 !important;
    }

    .btn-primary,
    .btn-danger {
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: none;
        cursor: pointer;
        color: white;
        border-radius: 4px;
    }

    .btn-primary {
        background-color: #319B72;
    }

    .btn-danger {
        background-color: #d33;
    }

    tr[data-href] {
        cursor: pointer;
    }

    tr[data-href]:hover {
        background-color: #f9f9f9;
    }
</style>

<div class="page-header">
    <div>
        <h2>Clients</h2>
        <p>View and manage clients</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <form action="{{ route('clients') }}" method="GET" style="position: relative; margin: 0;">
            <input type="text"
                name="search"
                id="searchInput"
                value="{{ request('search') }}"
                placeholder="Search clients..."
                style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search"
                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </form>

        {{-- Both Admin and Ops Manager can Add Clients, so this stays visible to all --}}
        <button class="btn-primary" id="addClientBtn">
            <i class="fas fa-plus"></i> Add Client
        </button>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="clientTableBody">
            @forelse ($clients as $client)
            <tr data-href="{{ route('clients.panel', $client->id) }}">
                <td>{{ $client->name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->phone ?? 'N/A' }}</td>
                <td>{{ $client->address ?? 'N/A' }}</td>
                <td>
                    {{-- Both Roles can Edit --}}
                    <button class="btn-primary edit-btn"
                        title="Edit Client"
                        data-id="{{ $client->id }}"
                        data-name="{{ $client->name }}"
                        data-email="{{ $client->email }}"
                        data-phone="{{ $client->phone }}"
                        data-address="{{ $client->address }}">
                        <i class="fas fa-edit"></i>
                    </button>

                    {{-- SECURITY UPDATE: Only 'Admin' can see the Delete button --}}
                    @if(auth()->user()->role === 'Admin')
                    <button class="btn-danger delete-btn"
                        title="Delete Client"
                        data-id="{{ $client->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:40px; color:#64748b; ">
                    No clients found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $clients->links() }}
    </div>
</div>

<div class="modal-overlay" id="clientModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Client</h3>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form class="modal-form">
            <input type="hidden" id="client_id">

            <div class="input-group">
                <label>Client Name</label>
                <input id="name" type="text" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input id="email" type="email" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input id="phone" placeholder="+09xxxxxxxxx" type="tel" pattern="(?:\+63|0)9\d{2}-\d{3}-\d{4}|(?:\+63|0)2\d{1}-\d{3}-\d{4}" required>
            </div>

            <div class="input-group">
                <label>Address</label>
                <input id="address" type="text" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="button" class="btn-save">Save Client</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- MODAL VARIABLES ---
        const modal = document.getElementById('clientModal');
        const modalTitle = document.getElementById('modalTitle');
        const addBtn = document.getElementById('addClientBtn');
        const closeBtn = document.querySelector('.close-modal-btn');
        const cancelBtn = document.querySelector('.btn-cancel');
        const saveBtn = document.querySelector('.btn-save');

        const clientIdField = document.getElementById('client_id');
        const nameField = document.getElementById('name');
        const emailField = document.getElementById('email');
        const phoneField = document.getElementById('phone');
        const addressField = document.getElementById('address');

        console.log('Elements found:', {
            modal: !!modal,
            addBtn: !!addBtn,
            saveBtn: !!saveBtn
        });

        // --- OPEN MODAL (ADD) ---
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Add button clicked');

                // Clear form
                clientIdField.value = '';
                nameField.value = '';
                emailField.value = '';
                phoneField.value = '';
                addressField.value = '';

                modalTitle.innerText = "Add New Client";
                modal.style.display = 'flex';
            });
        }

        // --- CLOSE MODAL ---
        const closeModal = () => {
            if (modal) modal.style.display = 'none';
        };
        
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        // Close on outside click
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        }

        // --- SAVE / UPDATE LOGIC ---

if (saveBtn) {
    saveBtn.addEventListener('click', async (e) => {
        e.preventDefault();

        // VALIDATION: Check if any field is empty
        const name = nameField.value.trim();
        const email = emailField.value.trim();
        const phone = phoneField.value.trim();
        const address = addressField.value.trim();

        if (!name || !email || !phone || !address) {
            Swal.fire({
                title: 'Error',
                html: '<div class="swal2-html-container">Fields are required.</div>',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }


        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                title: 'Error',
                html: '<div class="swal2-html-container">Wrong email format.</div>',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }

        const phoneRegex = /^(?:\+63|0)9\d{9}$/;
        const cleanPhone = phone.replace(/-/g, '').replace(/\s/g, '');
        if (!phoneRegex.test(cleanPhone)) {
            Swal.fire({
                title: 'Error',
                html: '<div class="swal2-html-container">Wrong phone format.</div>',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }

        // If validation passes, proceed with save
        const id = clientIdField.value;
        const formData = {
            name: name,
            email: email,
            phone: phone,
            address: address
        };

        let url = "{{ route('clients.create') }}";
        let method = 'POST';

        if (id) {
            url = `/clients/${id}`;
            method = 'PUT';
        }

        try {
            Swal.fire({
                title: 'Processing...',
                didOpen: () => Swal.showLoading()
            });

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
                modal.style.display = 'none';
                Swal.fire({
                    title: 'Success!',
                    text: result.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', result.message || 'Validation Failed', 'error');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'System error occurred', 'error');
        }
    });
}
        // --- TABLE ACTIONS ---
        const table = document.querySelector('.data-table');
        if (table) {
            table.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.edit-btn');
                const deleteBtn = e.target.closest('.delete-btn');
                const row = e.target.closest('tr[data-href]');

                if (editBtn) {
                    e.stopPropagation();
                    clientIdField.value = editBtn.dataset.id;
                    nameField.value = editBtn.dataset.name;
                    emailField.value = editBtn.dataset.email;
                    phoneField.value = editBtn.dataset.phone;
                    addressField.value = editBtn.dataset.address;

                    modalTitle.innerText = "Edit Client";
                    modal.style.display = 'flex';
                    return;
                }

                if (deleteBtn) {
                    e.stopPropagation();
                    const id = deleteBtn.dataset.id;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const response = await fetch(`/clients/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                });

                                if (response.ok) {
                                    Swal.fire('Deleted!', 'Client has been deleted.', 'success')
                                        .then(() => window.location.reload());
                                } else {
                                    Swal.fire('Error', 'Could not delete client.', 'error');
                                }
                            } catch (error) {
                                Swal.fire('Error', 'System error occurred', 'error');
                            }
                        }
                    });
                    return;
                }

                if (row) {
                    window.location.href = row.dataset.href;
                }
            });
        }
    });
</script>
@endpush
