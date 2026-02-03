@extends('layouts.app')

@section('title', 'Projects | Tanaman')

@push('styles')
    {{-- TomSelect CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    
    <style>
        .swal2-container {
            z-index: 20000 !important;
        }
        /* Specific styles for Projects page */
        .btn-primary, .btn-danger {
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
        .btn-primary { background-color: #319B72; }
        .btn-danger { background-color: #d33; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-badge.active { background-color: #e6f4ea; color: #1e7e34; }
        .status-badge.inactive { background-color: #fce8e6; color: #c62828; }
        
        tr[data-href] { cursor: pointer; }
        tr[data-href]:hover { background-color: #f9f9f9; }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h2>Projects</h2>
            <p>Manage all projects and their details</p>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <form action="{{ route('projects') }}" method="GET" style="position: relative; margin: 0;">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search projects..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
            </form>

            <button class="btn-primary" id="addProjectBtn">
                <i class="fas fa-plus"></i> Add Project
            </button>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Budget</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="projectsTableBody">
                @forelse($projects as $project)
                <tr data-href="{{ route('projects.panel', $project->id) }}">
                    <td>{{ $project->project_name }}</td>
                    <td>{{ $project->client->name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge {{ $project->is_active ? 'active' : 'inactive' }}">
                            {{ $project->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $project->project_end_date }}</td>
                    <td>₱{{ number_format($project->project_budget, 2) }}</td>
                    
                    <td>
                        <button class="btn-primary edit-btn"
                            title="Edit Project"
                            data-id="{{ $project->id }}"
                            data-name="{{ $project->project_name }}"
                            data-client-id="{{ $project->client_id ?? '' }}"
                            data-deadline="{{ $project->project_end_date }}"
                            data-budget="{{ $project->project_budget }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn-danger delete-btn"
                            title="Delete Project"
                            data-id="{{ $project->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                        No projects found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">
            {{ $projects->links() }}
        </div>
    </div>

    {{-- Add/Edit Project Modal --}}
    <div class="modal-overlay" id="addProjectModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Project</h3>
                <span class="close-modal-btn" id="closeProjectModal">&times;</span>
            </div>
            <form class="modal-form">
                <input type="hidden" id="project_id">

                <div class="input-group">
                    <label>Project Name</label>
                    <input type="text" id="p_name" placeholder="e.g. Garden Redesign" required>
                </div>
                
                <div class="input-group">
                    <label>Client</label>
                    <select id="p_client" placeholder="Select a client..." autocomplete="off">
                        <option value="">Select a client...</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group">
                    <label>Deadline</label>
                    <input type="date" id="p_deadline" required>
                </div>
                <div class="input-group">
                    <label>Budget (₱)</label>
                    <input type="number" id="p_budget" placeholder="e.g. 5000" step="0.01" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel">Cancel</button>
                    <button type="button" class="btn-save">Save Project</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- TomSelect JS --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        let clientSelect;

        document.addEventListener('DOMContentLoaded', function() {
            clientSelect = new TomSelect("#p_client", {
                create: false,
                sortField: { field: "text", direction: "asc" }
            });
        });

        const modal = document.getElementById('addProjectModal');
        const modalTitle = document.getElementById('modalTitle');
        const openBtn = document.getElementById('addProjectBtn');
        const closeBtn = document.querySelector('.close-modal-btn');
        const cancelBtn = document.querySelector('.btn-cancel');
        const saveBtn = document.querySelector('.btn-save');

        const idField = document.getElementById('project_id');
        const nameField = document.getElementById('p_name');
        const clientField = document.getElementById('p_client');
        const deadlineField = document.getElementById('p_deadline');
        const budgetField = document.getElementById('p_budget');

        // Open Modal (Add Mode)
        if (openBtn) {
            openBtn.addEventListener('click', () => {
                idField.value = '';
                nameField.value = '';
                deadlineField.value = '';
                budgetField.value = '';
                if (clientSelect) clientSelect.clear();

                modalTitle.innerText = "Add New Project";
                modal.style.display = 'flex';
            });
        }

        // Close Modal Logic
        const closeModal = () => { modal.style.display = 'none'; };
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        // Table Actions (Edit / Delete / Row Click)
        const table = document.querySelector('.data-table');
        
        if (table) {
            table.addEventListener('click', (e) => {
                
                // Edit Button Click
                const editBtn = e.target.closest('.edit-btn');
                if (editBtn) {
                    idField.value = editBtn.getAttribute('data-id');
                    nameField.value = editBtn.getAttribute('data-name');
                    deadlineField.value = editBtn.getAttribute('data-deadline');
                    budgetField.value = editBtn.getAttribute('data-budget');

                    const clientId = editBtn.getAttribute('data-client-id');
                    if (clientSelect && clientId) {
                        clientSelect.setValue(clientId);
                    }

                    modalTitle.innerText = "Edit Project";
                    modal.style.display = 'flex';
                    return; 
                }

                // Delete Button Click
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
                                const response = await fetch(`/projects/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                });

                                if (response.ok) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Project has been deleted successfully.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', 'Could not delete project.', 'error');
                                }
                            } catch (error) {
                                console.error(error);
                                Swal.fire('Error', 'System error occurred', 'error');
                            }
                        }
                    });
                    return; 
                }

                // Row Click (Navigate)
                const row = e.target.closest('tr[data-href]');
                if (row) {
                    window.location.href = row.dataset.href;
                }
            });
        }
        // Search logic
        const searchInput = document.getElementById('searchInput');
        
        const tableBody = document.getElementById('projectsTableBody'); 

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
        // Save Project Logic
        if (saveBtn) {
            saveBtn.addEventListener('click', async (e) => {
                e.preventDefault();

                const id = idField.value;
                const formData = {
                    project_name: nameField.value,
                    client_id: clientField.value,
                    project_end_date: deadlineField.value,
                    project_budget: budgetField.value
                };

                let url = "{{ route('projects.create') }}";
                let method = 'POST';

                if (id) {
                    url = `/projects/${id}`;
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
                            text: result.message || 'Project saved successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        let errorMessage = result.message || 'Validation Failed';
                        if (result.errors) {
                            errorMessage = Object.values(result.errors).flat().join('\n');
                        }
                        Swal.fire('Error', errorMessage, 'error');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'System error occurred', 'error');
                }
            });
        }
    </script>
@endpush