@extends('layouts.app')

@section('title', 'Invoices | Tanaman')

@section('content')

<style>
    /* --- Status Badges --- */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        text-align: center;
        min-width: 80px;
    }
    .status-badge.draft { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-badge.sent { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .status-badge.paid { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .status-badge.overdue { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    /* --- Actions Column --- */
    .actions-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end; 
        width: 100%;
    }

    /* --- Buttons --- */
    .btn-icon {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-icon:hover { transform: translateY(-1px); }
    
    .btn-send { background: #3b82f6; color: white; }
    .btn-send:hover { background: #2563eb; }
    
    .btn-pay { background: #10b981; color: white; }
    .btn-pay:hover { background: #059669; }
    
    .btn-menu-trigger {
        background: #f3f4f6;
        color: #4b5563;
    }
    .btn-menu-trigger:hover, .btn-menu-trigger.active {
        background: #e5e7eb;
        color: #1f2937;
    }

    /* --- Floating Dropdown Menu --- */
    .action-menu {
        display: none;
        position: fixed; 
        background: white;
        min-width: 170px; /* Slightly wider for Resend text */
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        z-index: 9999;
        padding: 5px;
        flex-direction: column;
    }
    
    .action-menu.show { display: flex; }

    .menu-item-btn {
        width: 100%;
        text-align: left;
        padding: 10px 12px;
        background: none;
        border: none;
        font-size: 0.9rem;
        color: #4b5563;
        cursor: pointer;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.1s;
    }
    .menu-item-btn:hover { background-color: #f9fafb; color: #111827; }
    .menu-item-btn.text-danger { color: #ef4444; }
    .menu-item-btn.text-danger:hover { background-color: #fef2f2; }

    /* --- Modal Styles --- */
    .modal-overlay { 
        display: none; 
        position: fixed; 
        top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0, 0, 0, 0.5); 
        z-index: 1000; 
        justify-content: center; align-items: center; 
    }
    
    .modal-box { 
        background: white; 
        width: 700px; 
        padding: 25px; 
        border-radius: 8px; 
        max-height: 90vh; 
        overflow-y: auto; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .modal-box::-webkit-scrollbar { display: none; }

    .item-row { display: flex; gap: 10px; margin-bottom: 8px; align-items: center; }
    .modal-section-title { font-weight: 600; color: #334155; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-top: 15px; }
    
    /* Buttons */
    .btn-save { background-color: #319B72; color: white; padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-cancel { background: transparent; border: 1px solid #cbd5e1; color: #64748b; padding: 8px 20px; border-radius: 6px; cursor: pointer; margin-right: 10px; }

    /* --- VIEW MODE STYLES --- */
    .view-mode-active #projectSelectContainer {
        display: none !important;
    }

    .view-mode-active .btn-save, 
    .view-mode-active #addItemBtn, 
    .view-mode-active .remove-btn {
        display: none !important;
    }
    
    .view-mode-active input, 
    .view-mode-active select {
        background-color: #f9fafb;
        color: #6b7280;
        border-color: #e5e7eb;
        pointer-events: none;
    }
</style>

<div class="page-header">
    <div>
        <h2>Invoices</h2>
        <p>View and manage invoices</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div style="position: relative;">
            <input type="text" id="searchInput" placeholder="Search invoices..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </div>
        <button class="btn-primary" id="addInvoiceBtn" style="background-color: #319B72; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-plus"></i> Create Invoice
        </button>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Project / Client</th>
                <th>Total Amount</th>
                <th>Created</th>
                <th>Due Date</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody id="invoiceTableBody">
            @forelse($invoices as $invoice)
            <tr>
                <td>
                    <div style="font-weight:600; color:#334155;">{{ $invoice->project->project_name ?? 'N/A' }}</div>
                    <div style="font-size:0.85em; color:#64748b;">{{ $invoice->client->name ?? 'Unknown' }}</div>
                </td>
                <td style="font-weight: 600;">₱{{ number_format($invoice->total_amount, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                <td>
                    <span class="status-badge {{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </td>
                <td>
                    <div class="actions-cell">
                        @if($invoice->status === 'draft')
                            <button class="btn-icon btn-send send-email-btn"
                                data-id="{{ $invoice->id }}"
                                data-email="{{ $invoice->client->email ?? '' }}"
                                title="Send to Client">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        @elseif($invoice->status === 'sent')
                            {{-- Just the Mark Paid button here --}}
                            <button class="btn-icon btn-pay mark-paid-btn"
                                data-id="{{ $invoice->id }}"
                                title="Mark as Paid">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <button class="btn-icon btn-menu-trigger" type="button" style="margin-left: 6px;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="action-menu">
                            {{-- If Paid, they can ONLY View, not Edit --}}
                            @if($invoice->status === 'paid')
                                <button class="menu-item-btn view-btn"
                                    data-id="{{ $invoice->id }}"
                                    data-json="{{ json_encode($invoice->load(['items', 'project'])) }}">
                                    <i class="fas fa-eye" style="color: #3b82f6;"></i> View Details
                                </button>
                            @else
                                <button class="menu-item-btn edit-btn"
                                    data-id="{{ $invoice->id }}"
                                    data-json="{{ json_encode($invoice->load(['items', 'project'])) }}">
                                    <i class="fas fa-edit" style="color: #3b82f6;"></i> Edit Details
                                </button>
                            @endif

                            {{-- NEW: Resend Option inside the menu --}}
                            @if($invoice->status === 'sent')
                                <button class="menu-item-btn send-email-btn btn-resend"
                                    data-id="{{ $invoice->id }}"
                                    data-email="{{ $invoice->client->email ?? '' }}">
                                    <i class="fas fa-paper-plane" style="color: #64748b;"></i> Resend Invoice
                                </button>
                            @endif

                            @if($invoice->status !== 'paid' && $invoice->status !== 'sent')
                                <button class="menu-item-btn mark-paid-btn" data-id="{{ $invoice->id }}">
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i> Mark as Paid
                                </button>
                            @endif

                            <div style="border-top: 1px solid #f3f4f6; margin: 4px 0;"></div>
                            <button class="menu-item-btn delete-btn text-danger" 
                                data-id="{{ $invoice->id }}"
                                data-status="{{ $invoice->status }}">
                                <i class="fas fa-trash-alt"></i> Delete Invoice
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">No invoices found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL --}}
<div class="modal-overlay" id="invoiceModal">
    <div class="modal-box">
        <div class="modal-header" style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <h3 style="margin:0;" id="modalTitle">Create New Invoice</h3>
            <span class="close-modal-btn" style="cursor:pointer; font-size:1.5em; color:#64748b;">&times;</span>
        </div>

        <form id="createInvoiceForm">
            <input type="hidden" id="invoiceId">

            <div class="input-group" id="projectSelectContainer">
                <label>Select Project</label>
                <select id="projectId" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; background-color:#fff;">
                    <option value="">-- Choose a Project --</option>
                    @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" data-client-id="{{ $proj->client_id }}" data-client-name="{{ $proj->client->name }}">{{ $proj->project_name }}</option>
                    @endforeach
                </select>
                <small style="color:#64748b;">Selecting a project will auto-fill the client and items.</small>
            </div>

            <div class="input-group" style="margin-top:15px;">
                <label>Client</label>
                <input type="text" id="clientNameDisplay" readonly style="background:#f1f5f9; color:#475569; font-weight:500;" placeholder="Auto-filled from Project">
                <input type="hidden" id="clientId">
            </div>

            <div style="display:flex; gap:15px; margin-top:15px;">
                <div class="input-group" style="flex:1;">
                    <label>Issue Date</label>
                    <input type="date" id="issueDate" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Due Date</label>
                    <input type="date" id="dueDate" required>
                </div>
            </div>

            <div class="modal-section-title">Billable Items</div>
            <div id="itemsContainer">
                <div style="color:#94a3b8; font-style:italic; padding:10px; text-align:center;" id="emptyStateMsg">Select a project to load items...</div>
            </div>

            <button type="button" id="addItemBtn" style="color:#319B72; background:none; border:none; cursor:pointer; margin-top:10px; font-weight:600; display:flex; align-items:center; gap:5px;">
                <i class="fas fa-plus-circle"></i> Add Custom Item
            </button>

            <div style="margin-top:20px; text-align:right; border-top: 2px solid #f1f5f9; padding-top:15px;">
                <label style="color:#64748b;">Total Amount:</label>
                <span id="displayTotal" style="font-size:1.4em; font-weight:bold; color:#1e293b; margin-left:10px;">₱0.00</span>
            </div>

            <div class="modal-actions" style="margin-top:25px; text-align:right;">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save" id="saveBtn">Create Invoice</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- DROPDOWN LOGIC ---
        document.querySelectorAll('.btn-menu-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('.action-menu.show').forEach(menu => {
                    if (menu !== this.nextElementSibling) {
                        menu.classList.remove('show');
                        menu.previousElementSibling.classList.remove('active');
                    }
                });
                const menu = this.nextElementSibling;
                const isAlreadyOpen = menu.classList.contains('show');
                if (!isAlreadyOpen) {
                    this.classList.add('active');
                    menu.classList.add('show');
                    const rect = this.getBoundingClientRect();
                    const menuHeight = 160; 
                    const spaceBelow = window.innerHeight - rect.bottom;
                    let rightPos = window.innerWidth - rect.right;
                    menu.style.right = rightPos + 'px';
                    menu.style.left = 'auto';
                    if (spaceBelow < menuHeight) {
                        menu.style.top = 'auto';
                        menu.style.bottom = (window.innerHeight - rect.top + 5) + 'px';
                    } else {
                        menu.style.bottom = 'auto';
                        menu.style.top = (rect.bottom + 5) + 'px';
                    }
                } else {
                     menu.classList.remove('show');
                     this.classList.remove('active');
                }
            });
        });

        window.addEventListener('scroll', () => {
             document.querySelectorAll('.action-menu.show').forEach(m => m.classList.remove('show'));
             document.querySelectorAll('.btn-menu-trigger').forEach(b => b.classList.remove('active'));
        }, true);

        window.addEventListener('click', () => {
             document.querySelectorAll('.action-menu.show').forEach(m => m.classList.remove('show'));
             document.querySelectorAll('.btn-menu-trigger').forEach(b => b.classList.remove('active'));
        });

        // --- MODAL & FORM LOGIC ---
        const modal = document.getElementById('invoiceModal');
        const modalTitle = document.getElementById('modalTitle');
        const openBtn = document.getElementById('addInvoiceBtn');
        const closeBtn = document.querySelector('.close-modal-btn');
        const cancelBtn = document.querySelector('.btn-cancel');
        const form = document.getElementById('createInvoiceForm');

        const invoiceIdInput = document.getElementById('invoiceId');
        const projectSelect = document.getElementById('projectId');
        const projectSelectContainer = document.getElementById('projectSelectContainer');
        const clientDisplay = document.getElementById('clientNameDisplay');
        const clientIdInput = document.getElementById('clientId');
        const itemsContainer = document.getElementById('itemsContainer');
        const totalDisplay = document.getElementById('displayTotal');
        const emptyStateMsg = document.getElementById('emptyStateMsg');
        const saveBtn = document.getElementById('saveBtn');

        let isEditMode = false;

        const resetModalState = () => {
            modal.style.display = 'none';
            form.reset();
            itemsContainer.innerHTML = '';
            itemsContainer.appendChild(emptyStateMsg);
            totalDisplay.innerText = '₱0.00';
            isEditMode = false;
            invoiceIdInput.value = '';
            
            // Re-show project selector (it might have been hidden by Edit or View mode)
            projectSelectContainer.style.display = 'block';
            
            form.classList.remove('view-mode-active');
            form.querySelectorAll('input, select').forEach(el => {
                el.disabled = false;
            });
            cancelBtn.innerText = "Cancel";
        };

        const openModal = () => { modal.style.display = 'flex'; };

        if (openBtn) {
            openBtn.addEventListener('click', () => {
                resetModalState();
                modalTitle.innerText = "Create New Invoice";
                saveBtn.innerText = "Create Invoice";
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('issueDate').value = today;
                const due = new Date();
                due.setDate(due.getDate() + 15);
                document.getElementById('dueDate').value = due.toISOString().split('T')[0];
                openModal();
            });
        }

        if (closeBtn) closeBtn.addEventListener('click', resetModalState);
        if (cancelBtn) cancelBtn.addEventListener('click', resetModalState);

        if (projectSelect) {
            projectSelect.addEventListener('change', async function() {
                const projectId = this.value;
                const option = this.options[this.selectedIndex];
                if (!projectId) {
                    if (!isEditMode) {
                        itemsContainer.innerHTML = '';
                        itemsContainer.appendChild(emptyStateMsg);
                        clientDisplay.value = '';
                        calculateTotal();
                    }
                    return;
                }
                if (option.dataset.clientId) {
                    clientIdInput.value = option.dataset.clientId;
                    clientDisplay.value = option.dataset.clientName;
                }
                if (isEditMode) return;
                itemsContainer.innerHTML = '<div style="color:#64748b; padding:10px; text-align:center;">Loading...</div>';
                try {
                    const response = await fetch(`/projects/${projectId}/invoice-data`);
                    const data = await response.json();
                    itemsContainer.innerHTML = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => createRow(item.description, item.quantity, item.price));
                    } else {
                        createRow('Consultation Service', 1, 0);
                    }
                    calculateTotal();
                } catch (error) {
                    itemsContainer.innerHTML = 'Error loading project data.';
                }
            });
        }

        function createRow(desc = '', qty = 1, price = 0) {
            if (document.getElementById('emptyStateMsg')) document.getElementById('emptyStateMsg').remove();
            const div = document.createElement('div');
            div.classList.add('item-row');
            div.innerHTML = `
                <input type="text" class="item-desc" value="${desc}" placeholder="Description" style="flex:2; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;" required>
                <input type="number" class="item-qty" value="${qty}" min="1" placeholder="Qty" style="flex:0.5; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;" required>
                <input type="number" class="item-price" value="${price}" min="0"  step="0.01" placeholder="Price" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;" required>
                <button type="button" class="remove-btn" style="color:#ef4444; border:none; background:none; cursor:pointer; font-size:1.1rem; padding:0 5px;" title="Remove Item">&times;</button>
            `;
            div.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateTotal));
            div.querySelector('.remove-btn').addEventListener('click', () => { div.remove(); calculateTotal(); });
            itemsContainer.appendChild(div);
            calculateTotal();
        }

        document.getElementById('addItemBtn').addEventListener('click', () => createRow());

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const q = parseFloat(row.querySelector('.item-qty').value) || 0;
                const p = parseFloat(row.querySelector('.item-price').value) || 0;
                total += (q * p);
            });
            totalDisplay.innerText = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        // Edit Mode
        document.addEventListener('click', function(e) {
            if(e.target.closest('.edit-btn')) {
                resetModalState();
                const btn = e.target.closest('.edit-btn');
                isEditMode = true;

                // HIDE PROJECT SELECTOR DURING EDIT
                projectSelectContainer.style.display = 'none';

                const invoiceData = JSON.parse(btn.getAttribute('data-json'));
                const invoiceId = btn.getAttribute('data-id');

                modalTitle.innerText = "Edit Invoice";
                saveBtn.innerText = "Update Invoice";
                invoiceIdInput.value = invoiceId;
                projectSelect.value = invoiceData.project_id;
                clientIdInput.value = invoiceData.client_id;
                clientDisplay.value = invoiceData.client ? invoiceData.client.name : 'Unknown';
                document.getElementById('issueDate').value = invoiceData.issue_date;
                document.getElementById('dueDate').value = invoiceData.due_date;

                itemsContainer.innerHTML = '';
                if (invoiceData.items && invoiceData.items.length > 0) {
                    invoiceData.items.forEach(item => createRow(item.description, item.quantity, item.price));
                } else {
                    createRow();
                }
                openModal();
                calculateTotal();
            }
        });

        // View Mode
        document.addEventListener('click', function(e) {
            if(e.target.closest('.view-btn')) {
                resetModalState();
                const btn = e.target.closest('.view-btn');
                const invoiceData = JSON.parse(btn.getAttribute('data-json'));

                modalTitle.innerText = "Invoice Details (Paid)";
                form.classList.add('view-mode-active');
                cancelBtn.innerText = "Close";

                projectSelect.value = invoiceData.project_id;
                clientIdInput.value = invoiceData.client_id;
                clientDisplay.value = invoiceData.client ? invoiceData.client.name : 'Unknown';
                document.getElementById('issueDate').value = invoiceData.issue_date;
                document.getElementById('dueDate').value = invoiceData.due_date;

                form.querySelectorAll('input, select').forEach(el => el.disabled = true);
                itemsContainer.innerHTML = '';
                if (invoiceData.items && invoiceData.items.length > 0) {
                    invoiceData.items.forEach(item => createRow(item.description, item.quantity, item.price));
                }
                itemsContainer.querySelectorAll('input').forEach(el => el.disabled = true);
                openModal();
                calculateTotal();
            }
        });

        // Mark Paid
        document.addEventListener('click', function(e) {
            if(e.target.closest('.mark-paid-btn')) {
                const btn = e.target.closest('.mark-paid-btn');
                const invoiceId = btn.getAttribute('data-id');
                Swal.fire({
                    title: 'Mark as Paid?',
                    text: "This will update the invoice status to Paid.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Mark Paid!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/invoices/${invoiceId}/pay`, {
                                method: 'PUT',
                                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' }
                            });
                            if (response.ok) {
                                Swal.fire({ icon: 'success', title: 'Paid!', timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
                            }
                        } catch (error) { Swal.fire('Error', 'System error occurred.', 'error'); }
                    }
                });
            }
        });

        // Send Email
        document.addEventListener('click', function(e) {
            if(e.target.closest('.send-email-btn')) {
                const btn = e.target.closest('.send-email-btn');
                const invoiceId = btn.getAttribute('data-id');
                const clientEmail = btn.getAttribute('data-email');
                if (!clientEmail) {
                    Swal.fire('Error', 'No email linked to this client.', 'error');
                    return;
                }
                
                // Customize title based on if it's a resend
                const isResend = btn.classList.contains('btn-resend');
                const titleText = isResend ? 'Resend Invoice?' : 'Send Invoice?';

                Swal.fire({
                    title: titleText,
                    text: `Send to ${clientEmail}?`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Yes, Send it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Sending...', didOpen: () => Swal.showLoading() });
                        try {
                            const response = await fetch(`/invoices/${invoiceId}/send`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' }
                            });
                            if (response.ok) {
                                Swal.fire({ icon: 'success', title: 'Sent!', timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
                            }
                        } catch (error) { Swal.fire('Error', 'System error occurred.', 'error'); }
                    }
                });
            }
        });

        // Delete
        document.addEventListener('click', function(e) {
            if(e.target.closest('.delete-btn')) {
                const btn = e.target.closest('.delete-btn');
                const id = btn.getAttribute('data-id');

                Swal.fire({
                    title: 'Delete Invoice?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/invoices/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' }
                            });
                            if (response.ok) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
                            }
                        } catch (error) { Swal.fire('Error', 'System error occurred.', 'error'); }
                    }
                });
            }
        });

        // Form Submit
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if(form.classList.contains('view-mode-active')) return;

            const originalText = saveBtn.innerText;
            saveBtn.innerText = 'Processing...';
            saveBtn.disabled = true;

            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                items.push({
                    desc: row.querySelector('.item-desc').value,
                    qty: row.querySelector('.item-qty').value,
                    price: row.querySelector('.item-price').value
                });
            });

            if (items.length === 0) {
                Swal.fire('Error', 'Please add at least one item.', 'warning');
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
                return;
            }

            const payload = {
                project_id: projectSelect.value,
                client_id: clientIdInput.value,
                issue_date: document.getElementById('issueDate').value,
                due_date: document.getElementById('dueDate').value,
                items: items
            };

            let url = "{{ route('invoices.store') }}";
            let method = "POST";
            if (isEditMode && invoiceIdInput.value) {
                url = `/invoices/${invoiceIdInput.value}`;
                method = "PUT";
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if (response.ok) {
                    resetModalState();
                    Swal.fire({ icon: 'success', title: 'Success!', timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
                } else {
                    const result = await response.json();
                    Swal.fire('Error', result.message || 'Validation failed.', 'error');
                    saveBtn.innerText = originalText;
                    saveBtn.disabled = false;
                }
            } catch (e) {
                Swal.fire('Error', 'System error occurred.', 'error');
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
            }
        });

        // Search
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('invoiceTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                for (let i = 0; i < rows.length; i++) {
                    let text = rows[i].innerText.toLowerCase();
                    rows[i].style.display = text.includes(filter) ? "" : "none";
                }
            });
        }
    });
</script>
@endpush