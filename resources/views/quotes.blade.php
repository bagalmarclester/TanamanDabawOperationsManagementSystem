@extends('layouts.app')

@section('title', 'Quotes | Tanaman')

@section('content')

<style>
    /* Status Badges */
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-badge.pending {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #ffedd5;
    }

    .status-badge.accepted {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #dcfce7;
    }

    .status-badge.rejected {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fee2e2;
    }

    /* Archived Status Design */
    .status-badge.archived {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    /* Table & Actions */
    .item-row {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
        align-items: center;
    }

    .ts-control {
        border-radius: 6px;
        padding: 10px;
        border: 1px solid #ddd;
    }

    .ts-dropdown {
        z-index: 99999 !important;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        transition: opacity 0.2s;
        margin-right: 6px;
    }

    .edit-btn {
        background-color: #10b981;
    }

    .edit-btn:hover {
        background-color: #059669;
    }

    .delete-btn {
        background-color: #ef4444;
    }

    .delete-btn:hover {
        background-color: #dc2626;
    }

    /* View Button Design */
    .view-btn {
        background-color: #10b981;
        color: #fff;
        border: 1px solid #dbeafe;
    }

    .view-btn:hover {
        background-color: #059669;
    }

    /* Helper to hide elements in View Mode */
    .view-mode-active .btn-remove,
    .view-mode-active #addItemBtn,
    .view-mode-active #saveQuoteBtn {
        display: none !important;
    }

    /* Visually indicate disabled inputs in view mode */
    .view-mode-active input {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }
</style>

<div class="page-header">
    <div>
        <h2>Quotes</h2>
        <p>View and manage quotes</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div style="position: relative;">
            <input type="text" id="searchInput" placeholder="Search quotes..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </div>
        <button class="btn-primary" id="addQuoteBtn">
            <i class="fas fa-plus"></i> Create Quote
        </button>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Client / Subject</th>
                <th>Total</th>
                <th>Created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="quotesTableBody">
            @forelse($quotes as $quote)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #334155;">{{ $quote->subject ?? 'Unknown Subject' }}</div>
                    <div style="font-size: 0.85rem; color: #64748b;">{{ $quote->client->name ?? 'Unknown Client' }}</div>
                </td>
                <td>₱{{ number_format($quote->total_amount, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</td>
                <td>
                    <span class="status-badge {{ strtolower($quote->status) }}">{{ ucfirst($quote->status) }}</span>
                </td>
                <td>
                    @if(strtolower($quote->status) === 'archived')
                    {{-- ARCHIVED: Show Eye, Hide Delete --}}
                    <button class="btn-action view-btn" data-quote="{{ json_encode($quote) }}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    @else
                    {{-- ACTIVE: Show Edit & Delete --}}
                    <button class="btn-action edit-btn" data-quote="{{ json_encode($quote) }}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action delete-btn" data-id="{{ $quote->id }}" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No quotes found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="addQuoteModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Create New Quote</h3>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form class="modal-form" id="createQuoteForm">
            <div class="input-group">
                <label>Client</label>
                <select id="clientId" required>
                    <option value="">Select a Client...</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>Subject</label>
                <input type="text" id="quoteSubject" placeholder="e.g. Garden Maintenance" required>
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="input-group" style="flex:1;">
                    <label>Quote Date</label>
                    <input type="date" id="quoteDate" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Valid Until</label>
                    <input type="date" id="validUntil" required>
                </div>
            </div>

            <div class="form-group">
                <div class="line-items-header" style="display:flex; justify-content:space-between; margin: 10px 0 5px 0;">
                    <label>Line Items</label>
                    <span class="add-item-link" id="addItemBtn" style="cursor:pointer; color:#319B72;">+ Add item</span>
                </div>
                <div id="itemsContainer"></div>
            </div>

            <div class="input-group" style="margin-top: 10px;">
                <label>Total Amount (₱)</label>
                <input type="text" id="displayTotal" placeholder="0.00" readonly style="font-weight: bold; background-color: #f8fafc;">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel">Close</button>
                <button type="submit" class="btn-save" id="saveQuoteBtn">Create Quote</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Initialize TomSelect
        let clientSelect;
        if (document.getElementById('clientId')) {
            clientSelect = new TomSelect("#clientId", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Select a Client..."
            });
        }

        const modal = document.getElementById('addQuoteModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('createQuoteForm');
        const itemsContainer = document.getElementById('itemsContainer');
        const totalInput = document.getElementById('displayTotal');
        const saveBtn = document.getElementById('saveQuoteBtn');
        const addItemBtn = document.getElementById('addItemBtn');

        let isEditMode = false;
        let editId = null;

        // --- Helper Functions ---

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                total += (qty * price);
            });
            totalInput.value = '₱ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2
            });
        }

        function createItemRow(desc = '', qty = 1, price = '') {
            const div = document.createElement('div');
            div.classList.add('item-row');
            div.innerHTML = `
                <input type="text" class="input-desc item-desc" placeholder="Description" value="${desc}" required style="flex:2; min-width:0;">
                <input type="number" class="input-small item-qty" placeholder="Qty" value="${qty}" min="1" required style="flex:0.8; min-width:0;">
                <input type="number" class="input-small item-price" placeholder="Price" step="0.01" value="${price}" min="0" required style="flex:1; min-width:0;">
                <button type="button" class="btn-remove" style="background:none; color:#ef4444; border:none; cursor:pointer; padding:0 5px; font-size: 1.2rem;">
                    <i class="far fa-trash-alt"></i>
                </button>
            `;

            // Add event listeners for calculation
            div.querySelectorAll('input').forEach(input => input.addEventListener('input', calculateTotal));

            // Remove functionality
            div.querySelector('.btn-remove').addEventListener('click', () => {
                div.remove();
                calculateTotal();
            });

            itemsContainer.appendChild(div);
            calculateTotal();
        }

        function resetModalState() {
            // Unlock TomSelect
            if (clientSelect) clientSelect.unlock();

            // Enable all inputs
            form.querySelectorAll('input').forEach(input => input.disabled = false);

            // Remove View Mode class (shows buttons again)
            form.classList.remove('view-mode-active');

            // Show Save Button and reset text
            saveBtn.style.display = 'inline-block';

            form.reset();
        }

        // --- Event Listeners ---
        document.getElementById('addQuoteBtn').addEventListener('click', () => {
            resetModalState();
            isEditMode = false;
            editId = null;
            modalTitle.innerText = "Create New Quote";
            saveBtn.innerText = "Create Quote";

            if (clientSelect) clientSelect.clear();

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('quoteDate').value = today;
            document.getElementById('validUntil').value = today;

            itemsContainer.innerHTML = '';
            createItemRow();
            modal.style.display = 'flex';
        });

        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                resetModalState();
                isEditMode = true;
                const quote = JSON.parse(editBtn.dataset.quote);
                editId = quote.id;

                modalTitle.innerText = `Edit Quote`;
                saveBtn.innerText = "Update Quote";
                if (clientSelect) clientSelect.setValue(quote.client_id);

                document.getElementById('quoteSubject').value = quote.subject || '';
                document.getElementById('quoteDate').value = quote.quote_date;
                document.getElementById('validUntil').value = quote.valid_until;

                itemsContainer.innerHTML = '';
                if (quote.items && quote.items.length > 0) {
                    quote.items.forEach(item => {
                        createItemRow(item.description, item.quantity, item.price);
                    });
                } else {
                    createItemRow();
                }
                modal.style.display = 'flex';
            }
        });

        document.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.view-btn');
            if (viewBtn) {
                resetModalState(); // Start clean

                const quote = JSON.parse(viewBtn.dataset.quote);

                // Set UI to "View Mode"
                modalTitle.innerText = "View Quote (Archived)";
                form.classList.add('view-mode-active'); // CSS handles hiding buttons/styling

                // Populate Data
                if (clientSelect) {
                    clientSelect.setValue(quote.client_id);
                    clientSelect.lock(); // Disable dropdown
                }

                document.getElementById('quoteSubject').value = quote.subject || '';
                document.getElementById('quoteDate').value = quote.quote_date;
                document.getElementById('validUntil').value = quote.valid_until;

                // Disable inputs
                form.querySelectorAll('input').forEach(input => input.disabled = true);

                // Populate Items (read-only)
                itemsContainer.innerHTML = '';
                if (quote.items && quote.items.length > 0) {
                    quote.items.forEach(item => {
                        createItemRow(item.description, item.quantity, item.price);
                    });
                } else {
                    // Show one empty row if none exist
                    createItemRow();
                }

                // Re-disable item inputs (since createItemRow makes them enabled by default)
                itemsContainer.querySelectorAll('input').forEach(input => input.disabled = true);

                modal.style.display = 'flex';
            }
        });

        // Close Modal Logic
        const closeModal = () => modal.style.display = 'none';
        document.querySelector('.close-modal-btn').addEventListener('click', closeModal);
        document.querySelector('.btn-cancel').addEventListener('click', closeModal);

        document.getElementById('addItemBtn').addEventListener('click', () => {
            // Only allow adding items if NOT in view mode
            if (!form.classList.contains('view-mode-active')) {
                createItemRow();
            }
        });

        // Form Submit (Create/Update)
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Safety check: Do not submit if in view mode
            if (form.classList.contains('view-mode-active')) return;

            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                items.push({
                    description: row.querySelector('.item-desc').value,
                    quantity: row.querySelector('.item-qty').value,
                    price: row.querySelector('.item-price').value
                });
            });

            const payload = {
                client_id: document.getElementById('clientId').value,
                subject: document.getElementById('quoteSubject').value,
                quote_date: document.getElementById('quoteDate').value,
                valid_until: document.getElementById('validUntil').value,
                items: items
            };

            let url = "{{ route('quotes.store') }}";
            let method = "POST";

            if (isEditMode) {
                url = `/quotes/${editId}`;
                method = "PUT";
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
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
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
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire('Error', result.message || 'Validation failed', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'System error occurred', 'error');
            }
        });

        // Delete Logic (Delegated)
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                Swal.fire({
                    title: 'Delete Quote?',
                    text: "You can restore this later.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`/quotes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            if (res.ok) window.location.reload();
                        } catch (err) {
                            console.error(err);
                        }
                    }
                });
            }
        });

        // Search Logic
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('quotesTableBody');
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
    });
</script>
@endpush