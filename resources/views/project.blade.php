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

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.85em;
        font-weight: 500;
    }

    .status-badge.active {
        background-color: #e6f4ea;
        color: #1e7e34;
    }

    .status-badge.inactive {
        background-color: #fce8e6;
        color: #c62828;
    }

    tr[data-href] {
        cursor: pointer;
    }

    tr[data-href]:hover {
        background-color: #f9f9f9;
    }
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
                        data-quote-id="{{ $project->quote_id }}"
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

            <div class="input-group" id="quoteContainer" style="background: #f8fafc; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 6px; margin-bottom: 15px;">
                <label style="color: #334155; font-weight: 600;">Link to Quote (Optional)</label>

                {{-- Added 'placeholder' for Tom Select --}}
                <select id="p_quote" placeholder="Search for a quote...">
                    <option value="">-- No Quote (Manual Entry) --</option>
                    @if(isset($pendingQuotes))
                    @foreach($pendingQuotes as $quote)
                    <option value="{{ $quote->id }}"
                        data-client="{{ $quote->client_id }}"
                        data-budget="{{ $quote->total_amount }}"
                        data-subject="{{ $quote->subject }}">
                        Quote #{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }} — {{ $quote->subject }} (₱{{ number_format($quote->total_amount) }})
                    </option>
                    @endforeach
                    @endif
                </select>
                <small style="color: #64748b; font-size: 0.8em;">Selecting a quote auto-fills Client & Budget</small>
            </div>

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
    let quoteSelect;

    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Initialize Client Dropdown
        if(document.getElementById('p_client')) {
            clientSelect = new TomSelect("#p_client", {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: "Select a Client..."
            });
        }

        // 2. Initialize Quote Dropdown (Used for Create Mode Auto-fill)
        if(document.getElementById('p_quote')) {
            quoteSelect = new TomSelect("#p_quote", {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: "Search for a quote...",
                allowEmptyOption: true,
                onChange: function(value) {
                    // This logic only runs when you pick a quote in the dropdown
                    const nameInput = document.getElementById('p_name');
                    const budgetInput = document.getElementById('p_budget');
                    
                    // Get data from the selected option
                    const originalOption = document.querySelector(`#p_quote option[value="${value}"]`);

                    if (value && originalOption) {
                        // --- QUOTE SELECTED: FILL & LOCK ---
                        const clientID = originalOption.getAttribute('data-client');
                        const budgetAmount = originalOption.getAttribute('data-budget');
                        const subject = originalOption.getAttribute('data-subject');

                        if(nameInput) { 
                            nameInput.value = subject; 
                            nameInput.readOnly = true; 
                            nameInput.style.backgroundColor = "#f1f5f9"; // Grey
                        }
                        if(budgetInput) { 
                            budgetInput.value = budgetAmount; 
                            budgetInput.readOnly = true; 
                            budgetInput.style.backgroundColor = "#f1f5f9"; 
                        }
                        if (clientSelect) { 
                            clientSelect.setValue(clientID); 
                            clientSelect.lock(); 
                        }
                    } else {
                        // --- MANUAL MODE: RESET & UNLOCK ---
                        if(nameInput) { 
                            nameInput.readOnly = false; 
                            nameInput.style.backgroundColor = "#fff"; 
                        }
                        if(budgetInput) { 
                            budgetInput.readOnly = false; 
                            budgetInput.style.backgroundColor = "#fff"; 
                        }
                        if (clientSelect) { 
                            clientSelect.unlock(); 
                            clientSelect.clear(); 
                        }
                    }
                }
            });
        }
    });

    // --- ELEMENTS ---
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
    const quoteContainer = document.getElementById('quoteContainer');

    // --- 1. OPEN MODAL (ADD MODE) ---
    if (openBtn) {
        openBtn.addEventListener('click', () => {
            idField.value = '';
            nameField.value = '';
            deadlineField.value = '';
            budgetField.value = '';
            
            // Show Quote Dropdown
            if(quoteContainer) quoteContainer.style.display = 'block';

            // Reset Quote Dropdown (Triggers onChange to unlock fields)
            if (quoteSelect) quoteSelect.clear(); 

            // Explicitly unlock just in case
            nameField.readOnly = false; nameField.style.backgroundColor = "#fff";
            budgetField.readOnly = false; budgetField.style.backgroundColor = "#fff";
            if(clientSelect) { clientSelect.unlock(); clientSelect.clear(); }

            modalTitle.innerText = "Add New Project";
            modal.style.display = 'flex';
        });
    }

    // --- 2. TABLE ACTIONS (EDIT / DELETE) ---
    const table = document.querySelector('.data-table');
    if (table) {
        table.addEventListener('click', (e) => {
            
            // --- EDIT BUTTON ---
            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                e.stopPropagation();

                // Get Data from Attributes
                const pId = editBtn.getAttribute('data-id');
                const pName = editBtn.getAttribute('data-name');
                const pDeadline = editBtn.getAttribute('data-deadline');
                const pBudget = editBtn.getAttribute('data-budget');
                const pClientId = editBtn.getAttribute('data-client-id');
                const linkedQuoteId = editBtn.getAttribute('data-quote-id'); // Critical

                // Set Common Values
                idField.value = pId;
                deadlineField.value = pDeadline;
                modalTitle.innerText = "Edit Project";
                
                // RULE 1: Hide Quote Dropdown in Edit Mode
                if(quoteContainer) quoteContainer.style.display = 'none';

                // RULE 2: STRICT LOCKING CHECK
                // Check if linkedQuoteId exists and is not null/empty
                if (linkedQuoteId && linkedQuoteId !== "null" && linkedQuoteId !== "") {
                    
                    // --- HAS QUOTE: LOCK FIELDS ---
                    nameField.value = pName;
                    nameField.readOnly = true;
                    nameField.style.backgroundColor = "#f1f5f9"; 

                    budgetField.value = pBudget;
                    budgetField.readOnly = true;
                    budgetField.style.backgroundColor = "#f1f5f9"; 

                    if (clientSelect) {
                        clientSelect.setValue(pClientId);
                        clientSelect.lock(); 
                    }

                } else {
                    // --- NO QUOTE: UNLOCK FIELDS ---
                    nameField.value = pName;
                    nameField.readOnly = false;
                    nameField.style.backgroundColor = "#fff";

                    budgetField.value = pBudget;
                    budgetField.readOnly = false;
                    budgetField.style.backgroundColor = "#fff";

                    if (clientSelect) {
                        clientSelect.unlock();
                        clientSelect.setValue(pClientId);
                    }
                }

                // Deadline is always editable
                deadlineField.readOnly = false;

                modal.style.display = 'flex';
                return;
            }

            // --- DELETE BUTTON ---
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.stopPropagation();
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
                                Swal.fire('Deleted!', 'Project has been deleted.', 'success').then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', 'Could not delete project.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                        }
                    }
                });
                return;
            }

            // --- ROW CLICK (Navigate) ---
            const row = e.target.closest('tr[data-href]');
            // Safety check: Don't navigate if clicked a button
            if (row && !e.target.closest('button') && !e.target.closest('.btn-action')) {
                window.location.href = row.dataset.href;
            }
        });
    }

    // --- 3. SAVE LOGIC ---
    if (saveBtn) {
        saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            const id = idField.value;
            
            // Base Data
            const formData = {
                project_name: nameField.value,
                client_id: clientField.value,
                project_end_date: deadlineField.value,
                project_budget: budgetField.value,
            };

            let url = "{{ route('projects.create') }}"; 
            let method = 'POST';

            if (id) {
                // UPDATE MODE
                url = `/projects/${id}`;
                method = 'PUT';
                // NOTE: We do NOT send quote_id here to avoid breaking the link
            } else {
                // CREATE MODE: Send the quote ID
                formData.quote_id = document.getElementById('p_quote') ? document.getElementById('p_quote').value : null;
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

    // --- CLOSE MODAL ---
    const closeModal = () => { modal.style.display = 'none'; };
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // --- SEARCH LOGIC ---
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('projectsTableBody');
    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                let textContent = rows[i].innerText.toLowerCase();
                rows[i].style.display = textContent.includes(filter) ? "" : "none";
            }
        });
    }
</script>
@endpush