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
                    <button class="btn-primary edit-btn"
                        title="Edit Client"
                        data-id="{{ $client->id }}"
                        data-name="{{ $client->name }}"
                        data-email="{{ $client->email }}"
                        data-phone="{{ $client->phone }}"
                        data-address="{{ $client->address }}">
                        <i class="fas fa-edit"></i>
                    </button>

                    <button class="btn-danger delete-btn"
                        title="Delete Client"
                        data-id="{{ $client->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
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

{{-- MODAL (UNCHANGED) --}}
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
                <input id="phone" type="tel">
            </div>

            <div class="input-group">
                <label>Address</label>
                <input id="address" type="text">
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
    const profileTrigger = document.getElementById('profile-trigger');
    const dropdownMenu = document.getElementById('profile-dropdown');
    const dropdownIcon = document.querySelector('.dropdown-icon');

    if (profileTrigger) {
        profileTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
            dropdownIcon.style.transform = dropdownMenu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        });

        window.addEventListener('click', function(e) {
            if (!dropdownMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                dropdownIcon.style.transform = 'rotate(0deg)';
            }
        });
    }

    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && e.target !== menuToggle) {
                sidebar.classList.remove('active');
            }
        });
    }

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

    // search logic
    const searchInput = document.getElementById('searchInput');

    const tableBody = document.getElementById('clientTableBody');

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let textContent = rows[i].innerText.toLowerCase();

                // Toggle visibility based on search match
                if (textContent.includes(filter)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });
    }

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            clientIdField.value = '';
            nameField.value = '';
            emailField.value = '';
            phoneField.value = '';
            addressField.value = '';

            modalTitle.innerText = "Add New Client";
            modal.style.display = 'flex';
        });
    }

    const table = document.querySelector('.data-table');
    if (table) {
        table.addEventListener('click', (e) => {

            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                clientIdField.value = editBtn.getAttribute('data-id');
                nameField.value = editBtn.getAttribute('data-name');
                emailField.value = editBtn.getAttribute('data-email');
                phoneField.value = editBtn.getAttribute('data-phone');
                addressField.value = editBtn.getAttribute('data-address');

                modalTitle.innerText = "Edit Client";
                modal.style.display = 'flex';
                return;
            }

            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                const id = deleteBtn.getAttribute('data-id');

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
                                Swal.fire({
                                        title: 'Deleted!',
                                        text: result.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', 'Could not delete client.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire('Error', 'System error occurred', 'error');
                        }
                    }
                });
                return;
            }

            const row = e.target.closest('tr[data-href]');
            if (row) {
                window.location.href = row.dataset.href;
            }
        });
    }


    const closeModal = () => {
        if (modal) modal.style.display = 'none';
    };
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    if (saveBtn) {
        saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            const id = clientIdField.value;
            const formData = {
                name: nameField.value,
                email: emailField.value,
                phone: phoneField.value,
                address: addressField.value
            };

            let url = "{{ route('clients.create') }}";
            let method = 'POST';

            if (id) {
                url = `/clients/${id}`;
                method = 'PUT';
            }

            try {
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
</script>
@endpush