@extends('layouts.app')

@section('title', 'Invoices | Tanaman')

@section('content')

{{-- 1. STYLES --}}
<style>
    /* Status Badges */
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.85em; font-weight: 600; text-transform: capitalize; }
    .status-badge.draft { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .status-badge.sent { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .status-badge.paid { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .status-badge.overdue { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    /* Modal Styling */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-box { background: white; width: 700px; padding: 25px; border-radius: 8px; max-height: 90vh; overflow-y: auto; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); scrollbar-width: none; -ms-overflow-style: none; }
    .modal-box::-webkit-scrollbar { display: none; }

    /* Dynamic Item Row */
    .item-row { display: flex; gap: 10px; margin-bottom: 8px; align-items: center; }
    .modal-section-title { font-weight: 600; color: #334155; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-top: 15px; }

    /* Actions */
    .btn-paid { background-color: #10b981; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; transition: 0.2s; }
    .btn-paid:hover { background-color: #059669; }
    .btn-paid:disabled { background-color: #cbd5e1; cursor: not-allowed; }
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
        <button class="btn-primary" id="addInvoiceBtn">
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
                <th>Actions</th>
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
                    <div style="display: flex; gap: 6px;">
                        {{-- 1. SEND EMAIL (Blue) --}}
                        <button class="btn-action send-email-btn" 
                            data-id="{{ $invoice->id }}" 
                            data-email="{{ $invoice->client->email ?? '' }}"
                            title="Send to Client"
                            style="background-color:#3b82f6; color:white; border:none; padding:6px 10px; border-radius:6px; cursor:pointer;">
                            <i class="fas fa-paper-plane"></i>
                        </button>

                        @if($invoice->status !== 'paid')
                            {{-- 2. MARK PAID (Green) --}}
                            <button class="btn-action mark-paid-btn" 
                                data-id="{{ $invoice->id }}" 
                                title="Mark as Paid"
                                style="background-color:#10b981; color:white; border:none; padding:6px 10px; border-radius:6px; cursor:pointer;">
                                <i class="fas fa-check-circle"></i>
                            </button>

                            {{-- 3. EDIT (Gray) --}}
                            <button class="btn-action edit-btn" 
                                data-json="{{ json_encode($invoice->load('items')) }}" 
                                title="Edit Invoice"
                                style="background-color:#64748b; color:white; border:none; padding:6px 10px; border-radius:6px; cursor:pointer;">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif

                        {{-- 4. DELETE (Red) --}}
                        <button class="btn-action delete-btn" 
                            data-id="{{ $invoice->id }}" 
                            title="Delete Invoice"
                            style="background-color:#ef4444; color:white; border:none; padding:6px 10px; border-radius:6px; cursor:pointer;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No invoices found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 2. CREATE INVOICE MODAL --}}
<div class="modal-overlay" id="invoiceModal">
    <div class="modal-box">
        <div class="modal-header" style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <h3 style="margin:0;">Create New Invoice</h3>
            <span class="close-modal-btn" style="cursor:pointer; font-size:1.5em; color:#64748b;">&times;</span>
        </div>

        <form id="createInvoiceForm">
            <div class="input-group">
                <label>Select Project</label>
                <select id="projectId" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; background-color:#fff;">
                    <option value="">-- Choose a Project --</option>
                    @foreach($projects as $proj)
                    <option value="{{ $proj->id }}">{{ $proj->project_name }}</option>
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

        // --- VARIABLES ---
        const modal = document.getElementById('invoiceModal');
        const openBtn = document.getElementById('addInvoiceBtn');
        const closeBtn = document.querySelector('.close-modal-btn');
        const cancelBtn = document.querySelector('.btn-cancel');
        const form = document.getElementById('createInvoiceForm');

        const projectSelect = document.getElementById('projectId');
        const clientDisplay = document.getElementById('clientNameDisplay');
        const clientIdInput = document.getElementById('clientId');
        const itemsContainer = document.getElementById('itemsContainer');
        const totalDisplay = document.getElementById('displayTotal');
        const emptyStateMsg = document.getElementById('emptyStateMsg');

        // --- OPEN / CLOSE MODAL ---
        const openModal = () => {
            modal.style.display = 'flex';
            const today = new Date();
            document.getElementById('issueDate').valueAsDate = today;
            const due = new Date();
            due.setDate(today.getDate() + 15);
            document.getElementById('dueDate').valueAsDate = due;
        };
        const closeModal = () => modal.style.display = 'none';

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // --- 1. SMART FETCH LOGIC (Project -> Items) ---
        if (projectSelect) {
            projectSelect.addEventListener('change', async function() {
                const projectId = this.value;
                if (!projectId) {
                    itemsContainer.innerHTML = '';
                    itemsContainer.appendChild(emptyStateMsg);
                    clientDisplay.value = '';
                    calculateTotal();
                    return;
                }

                itemsContainer.innerHTML = '<div style="color:#64748b; padding:10px; text-align:center;"><i class="fas fa-spinner fa-spin"></i> Loading project details...</div>';

                try {
                    const response = await fetch(`/projects/${projectId}/invoice-data`);
                    const data = await response.json();

                    clientDisplay.value = data.client_name || 'Unknown Client';
                    clientIdInput.value = data.client_id;

                    itemsContainer.innerHTML = ''; 
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            createRow(item.description, item.quantity, item.price);
                        });
                    } else {
                        createRow('Consultation Service', 1, 0); 
                    }
                    calculateTotal();
                } catch (error) {
                    console.error("Fetch error:", error);
                    itemsContainer.innerHTML = '<div style="color:#ef4444; padding:10px;">Error loading project data. Please try again.</div>';
                }
            });
        }

        // --- 2. DYNAMIC ROWS ---
        function createRow(desc = '', qty = 1, price = 0) {
            const div = document.createElement('div');
            div.classList.add('item-row');
            div.innerHTML = `
                <input type="text" class="item-desc" value="${desc}" placeholder="Description" style="flex:2; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;" required>
                <input type="number" class="item-qty" value="${qty}" min="1" placeholder="Qty" style="flex:0.5; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;" required>
                <input type="number" class="item-price" value="${price}" min="0" step="0.01" placeholder="Price" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;" required>
                <button type="button" class="remove-btn" style="color:#ef4444; border:none; background:none; cursor:pointer; font-size:1.1rem; padding:0 5px;" title="Remove Item">&times;</button>
            `;
            div.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateTotal));
            div.querySelector('.remove-btn').addEventListener('click', () => { div.remove(); calculateTotal(); });
            itemsContainer.appendChild(div);
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

        // --- 3. SUBMIT FORM ---
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerText;

            saveBtn.innerText = 'Creating...';
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

            try {
                const response = await fetch("{{ route('invoices.store') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    modal.style.display = 'none';
                    Swal.fire({ title: 'Success!', text: 'Invoice created successfully.', icon: 'success', timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
                } else {
                    const result = await response.json();
                    Swal.fire('Error', result.message || 'Validation failed.', 'error');
                    saveBtn.innerText = originalText;
                    saveBtn.disabled = false;
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'System error occurred.', 'error');
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
            }
        });

        // --- 4. MARK AS PAID LOGIC ---
        document.querySelectorAll('.mark-paid-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const invoiceId = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Mark as Paid?',
                    text: "This will update the invoice status to Paid. This action cannot be undone.",
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
                                Swal.fire('Paid!', 'Invoice marked as paid.', 'success').then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', 'Could not update invoice.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire('Error', 'System error occurred.', 'error');
                        }
                    }
                });
            });
        });

        // --- 5. SEND EMAIL LOGIC (New) ---
        document.querySelectorAll('.send-email-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const invoiceId = this.getAttribute('data-id');
                const clientEmail = this.getAttribute('data-email');

                if (!clientEmail) {
                    Swal.fire('Error', 'This client does not have an email address linked.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Send Invoice?',
                    text: `Send this invoice to ${clientEmail}?`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Send it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        // Show loading because SMTP is slow
                        Swal.fire({
                            title: 'Sending...',
                            text: 'Please wait while we email the client.',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        try {
                            const response = await fetch(`/invoices/${invoiceId}/send`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await response.json();

                            if (response.ok) {
                                Swal.fire('Sent!', 'The invoice has been emailed successfully.', 'success')
                                .then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', data.message || 'Could not send email.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire('Error', 'System error occurred.', 'error');
                        }
                    }
                });
            });
        });

        // --- 6. DELETE LOGIC ---
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Delete Invoice?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/invoices/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' }
                            });
                            if (response.ok) {
                                Swal.fire('Deleted!', 'Invoice has been deleted.', 'success').then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', 'Could not delete invoice.', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                        }
                    }
                });
            });
        });

        // --- SEARCH LOGIC ---
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