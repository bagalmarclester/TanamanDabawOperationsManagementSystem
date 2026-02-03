@extends('layouts.app')

@section('title', 'Inventory | Tanaman')

@section('content')

<style>
    /* Reuse styles from your Client System */
    .swal2-container {
        z-index: 20000 !important;
    }

    .btn-secondary {
        background-color: #64748b; color: white; padding: 8px 12px;
        border: none; border-radius: 4px; cursor: pointer;
    }
    .btn-secondary:hover { background-color: #475569; }

    .btn-primary {
        background-color: #319B72;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Inventory Specific Styles */
    .btn-stock-in {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-stock-in:hover {
        background: #bbf7d0;
    }

    .btn-stock-out {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-stock-out:hover {
        background: #fecaca;
    }

    .stock-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-red {
        background-color: #fef2f2;
        border-bottom: 1px solid #fee2e2;
    }

    .bg-green {
        background-color: #d1fae5;
        color: #047857;
        padding: 10px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-red-soft {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 10px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stock-info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .stock-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #334155;
    }

    .sku-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #64748b;
    }
</style>

<div class="page-header">
    <div>
        <h2>Inventory</h2>
        <p>Stock levels and product catalog</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        {{-- Search Form --}}
        <form action="{{ route('inventory') }}" method="GET" style="position: relative; margin: 0;">
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                id="searchInput"
                placeholder="Search inventory..."
                style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </form>
        <button class="btn-secondary" id="viewHistoryBtn">
            <i class="fas fa-history"></i>
        </button>
        <button class="btn-primary" id="addItemBtn">
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
</div>

<div class="table-container">
    <table class="data-table" >
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock Level</th>
                <th>Adjustment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody">
            @forelse($inventory as $item)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #334155;">{{ $item->item_name }}</div>
                </td>
                <td>
                    <div style="color: #64748b; font-weight: 500;">{{ $item->sku }}</div>
                </td>
                <td>
                    {{ $item->category->name ?? 'Uncategorized' }}
                </td>
                <td>₱{{ number_format($item->price, 2) }}</td>
                <td>
                    <span
                        @style([ 'font-weight: 700' , 'font-size: 1.1rem' , 'color: #ef4444'=> $item->stock_level < 5, 'color: #334155'=> $item->stock_level >= 5,
                            ])>
                            {{ $item->stock_level }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-stock-in"
                            data-id="{{ $item->id }}"
                            data-sku="{{ $item->sku }}"
                            data-stock="{{ $item->stock_level }}">
                            <i class="fas fa-arrow-up" style="font-size: 0.7rem;"></i> In
                        </button>
                        <button class="btn-stock-out"
                            data-id="{{ $item->id }}"
                            data-sku="{{ $item->sku }}"
                            data-stock="{{ $item->stock }}">
                            <i class="fas fa-arrow-down" style="font-size: 0.7rem;"></i> Out
                        </button>
                    </div>
                </td>
                <td>
                    {{-- Edit Button --}}
                    <button class="action-btn edit-btn"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->item_name }}"
                        data-sku="{{ $item->sku }}"
                        data-category="{{ $item->category_id }}"
                        data-price="{{ $item->price }}"
                        data-stock="{{ $item->stock }}">
                        <i class="fas fa-pen"></i>
                    </button>

                    {{-- Delete Button --}}
                    <button class="action-btn delete delete-btn"
                        data-id="{{ $item->id }}">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No inventory items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="historyModal">
    {{-- Made this modal wider (max-width: 800px) so the table fits well --}}
    <div class="modal-box" style="max-width: 800px; width: 90%;">
        <div class="modal-header">
            <h3>Transaction History</h3>
            <span class="close-modal-btn" id="closeHistory">&times;</span>
        </div>
        
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="background: #f8fafc;">Date & Time</th>
                        <th style="background: #f8fafc;">Item Name</th>
                        <th style="background: #f8fafc;">Type</th>
                        <th style="background: #f8fafc;">Qty</th>
                        <th style="background: #f8fafc;">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($transactions) && $transactions->count() > 0)
                        @foreach($transactions as $log)
                        <tr>
                            <td style="color: #64748b; font-size: 0.9rem;">{{ $log->created_at->format('M d, h:i A') }}</td>
                            <td style="font-weight: 600;">{{ $log->inventory->item_name ?? 'Deleted Item' }}</td>
                            <td>
                                @if($log->type === 'IN')
                                    <span class="badge-in">IN</span>
                                @else
                                    <span class="badge-out">OUT</span>
                                @endif
                            </td>
                            <td style="font-weight: 700;">{{ $log->quantity }}</td>
                            <td style="color: #475569; font-size: 0.9rem;">{{ $log->reason ?? '--' }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No history available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="modal-actions" style="border-top: 1px solid #eee; margin-top: 0; padding-top: 20px;">
            <button class="btn-cancel" id="closeHistoryBtn">Close</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="addItemModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="itemModalTitle">Add New Item</h3>
            <span class="close-modal-btn" id="closeAddItem">&times;</span>
        </div>

        <form class="modal-form" id="itemForm">
            <input type="hidden" id="item_id">

            <div class="input-group">
                <label>Item Name</label>
                <input type="text" id="itemName" placeholder="e.g. Fertilizer" required>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>SKU</label>
                    <input type="text" id="itemSku" placeholder="e.g. FUR-001" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Category</label>
                    <select id="itemCategory" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>Price (₱)</label>
                    <input type="number" id="itemPrice" step="0.01" placeholder="0.00" required>
                </div>
                {{-- Initial Stock is only visible on CREATE, hidden on UPDATE --}}
                <div class="input-group" style="flex:1;" id="initialStockGroup">
                    <label>Initial Stock</label>
                    <input type="number" id="itemStock" placeholder="0">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" id="cancelAddItem">Cancel</button>
                <button type="submit" class="btn-save">Save Item</button>
            </div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="stockInModal">
    <div class="modal-box" style="max-width: 450px;">
        <div class="stock-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="header-icon-box bg-green"><i class="fas fa-arrow-up"></i></div>
                <h3 style="margin: 0; color: #064e3b;">Stock In</h3>
            </div>
            <span class="close-modal-btn" id="closeStockIn">&times;</span>
        </div>

        <div style="padding: 0 20px 20px 20px;">
            <div class="stock-info-card">
                <div>
                    <label>CURRENT STOCK</label>
                    <div class="stock-value" id="inModalCurrentStock">--</div>
                </div>
                <div style="text-align: right;">
                    <label>ITEM SKU</label>
                    <div class="sku-value" id="inModalSku">--</div>
                </div>
            </div>

            <form class="stock-form" id="stockInForm">
                <input type="hidden" id="inItemId">
                <div class="input-group">
                    <label>QUANTITY TO ADD</label>
                    <input type="number" id="inQuantity" class="dark-input" placeholder="0" min="1" required>
                </div>
                <div class="input-group">
                    <label>REASON / REFERENCE</label>
                    <div class="input-with-icon">
                        <input type="text" id="inReason" class="dark-input" placeholder="e.g. Shipment #1234" style="width:100%; padding: 10px;">
                    </div>
                </div>
                <div class="modal-actions" style="margin-top: 25px; align-items: center;">
                    <button type="button" class="btn-cancel" id="cancelStockIn">Cancel</button>
                    <button type="submit" class="btn-save" style="background-color: #10b981;">Confirm Stock In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="stockOutModal">
    <div class="modal-box" style="max-width: 450px;">
        <div class="stock-header header-red">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="header-icon-box bg-red-soft"><i class="fas fa-arrow-down"></i></div>
                <h3 style="margin: 0; color: #7f1d1d;">Stock Out</h3>
            </div>
            <span class="close-modal-btn" id="closeStockOut">&times;</span>
        </div>

        <div style="padding: 0 20px 20px 20px;">
            <div class="stock-info-card">
                <div>
                    <label>CURRENT STOCK</label>
                    <div class="stock-value" id="outModalCurrentStock">--</div>
                </div>
                <div style="text-align: right;">
                    <label>ITEM SKU</label>
                    <div class="sku-value" id="outModalSku">--</div>
                </div>
            </div>

            <form class="stock-form" id="stockOutForm">
                <input type="hidden" id="outItemId">
                <div class="input-group">
                    <label>QUANTITY TO REMOVE</label>
                    <input type="number" id="outQuantity" class="dark-input" placeholder="0" min="1" required>
                </div>
                <div class="input-group">
                    <label>REASON / REFERENCE</label>
                    <div class="input-with-icon">
                        <input type="text" id="outReason" class="dark-input" placeholder="e.g. Sales Order #9988" style="width:100%; padding: 10px;">
                    </div>
                </div>
                <div class="modal-actions" style="margin-top: 25px; align-items: center;">
                    <button type="button" class="btn-cancel" id="cancelStockOut">Cancel</button>
                    <button type="submit" class="btn-save btn-red" style="background-color: #ef4444; color:white;">Confirm Stock Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- Shared Utilities ---
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showSuccess(message) {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        }

        function showError(message) {
            Swal.fire('Error', message || 'Something went wrong', 'error');
        }

        const historyModal = document.getElementById('historyModal');
        const viewHistoryBtn = document.getElementById('viewHistoryBtn');
        const closeHistory = document.getElementById('closeHistory');
        const closeHistoryBtn = document.getElementById('closeHistoryBtn');
        
        // History Modal
        if(viewHistoryBtn) viewHistoryBtn.addEventListener('click', () => historyModal.style.display = 'flex');
        if(closeHistory) closeHistory.addEventListener('click', () => historyModal.style.display = 'none');
        if(closeHistoryBtn) closeHistoryBtn.addEventListener('click', () => historyModal.style.display = 'none');

        // Add / Edit Item Logic ---
        const addItemModal = document.getElementById('addItemModal');
        const itemForm = document.getElementById('itemForm');

        // Buttons
        document.getElementById('addItemBtn').addEventListener('click', () => {
            // Reset Form for "Add"
            document.getElementById('item_id').value = '';
            document.getElementById('itemForm').reset();
            document.getElementById('itemModalTitle').innerText = 'Add New Item';
            document.getElementById('initialStockGroup').style.display = 'block'; // Show stock input
            addItemModal.style.display = 'flex';
        });

        // Close/Cancel
        const closeItem = () => addItemModal.style.display = 'none';
        document.getElementById('closeAddItem').addEventListener('click', closeItem);
        document.getElementById('cancelAddItem').addEventListener('click', closeItem);

        // Edit Button Click
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('item_id').value = btn.dataset.id;
                document.getElementById('itemName').value = btn.dataset.name;
                document.getElementById('itemSku').value = btn.dataset.sku;
                document.getElementById('itemCategory').value = btn.dataset.category;
                document.getElementById('itemPrice').value = btn.dataset.price;

                // Hide Stock input during edit (Stock adjustments should use In/Out)
                document.getElementById('initialStockGroup').style.display = 'none';

                document.getElementById('itemModalTitle').innerText = 'Edit Item';
                addItemModal.style.display = 'flex';
            });
        });

        // Save Item (Create or Update)
        itemForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('item_id').value;
            const isEdit = !!id;

            const url = isEdit ? `/inventory/${id}` : "{{ route('inventory') }}"; // Adjust route if needed
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                name: document.getElementById('itemName').value,
                sku: document.getElementById('itemSku').value,
                category_id: document.getElementById('itemCategory').value,
                price: document.getElementById('itemPrice').value,
                stock: document.getElementById('itemStock').value || 0
            };

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok) showSuccess(data.message);
                else showError(data.message || 'Validation failed');
            } catch (err) {
                showError('System error occurred');
            }
        });

        // Delete Item Logic ---
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`/inventory/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            const data = await res.json();
                            if (res.ok) showSuccess(data.message);
                            else showError(data.message);
                        } catch (err) {
                            showError('System error');
                        }
                    }
                });
            });
        });

        // Stock In Logic ---
        const stockInModal = document.getElementById('stockInModal');
        const closeStockIn = () => stockInModal.style.display = 'none';
        document.getElementById('closeStockIn').addEventListener('click', closeStockIn);
        document.getElementById('cancelStockIn').addEventListener('click', closeStockIn);

        document.querySelectorAll('.btn-stock-in').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('inItemId').value = btn.dataset.id;
                document.getElementById('inModalSku').innerText = btn.dataset.sku;
                document.getElementById('inModalCurrentStock').innerText = btn.dataset.stock;
                document.getElementById('stockInForm').reset();
                stockInModal.style.display = 'flex';
            });
        });

        document.getElementById('stockInForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('inItemId').value;
            try {
                const res = await fetch(`/inventory/${id}/stock-in`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        quantity: document.getElementById('inQuantity').value,
                        reason: document.getElementById('inReason').value
                    })
                });
                const data = await res.json();
                if (res.ok) showSuccess(data.message);
                else showError(data.message);
            } catch (err) {
                showError('System Error');
            }
        });

        // Stock Out Logic ---
        const stockOutModal = document.getElementById('stockOutModal');
        const closeStockOut = () => stockOutModal.style.display = 'none';
        document.getElementById('closeStockOut').addEventListener('click', closeStockOut);
        document.getElementById('cancelStockOut').addEventListener('click', closeStockOut);

        document.querySelectorAll('.btn-stock-out').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('outItemId').value = btn.dataset.id;
                document.getElementById('outModalSku').innerText = btn.dataset.sku;
                document.getElementById('outModalCurrentStock').innerText = btn.dataset.stock;
                document.getElementById('stockOutForm').reset();
                stockOutModal.style.display = 'flex';
            });
        });

        // Search logic
        const searchInput = document.getElementById('searchInput');
        
        const tableBody = document.getElementById('inventoryTableBody'); 

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

        document.getElementById('stockOutForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('outItemId').value;
            try {
                const res = await fetch(`/inventory/${id}/stock-out`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        quantity: document.getElementById('outQuantity').value,
                        reason: document.getElementById('outReason').value
                    })
                });
                const data = await res.json();
                if (res.ok) showSuccess(data.message);
                else showError(data.message); // Will show "Insufficient stock" from controller
            } catch (err) {
                showError('System Error');
            }
        });

        // Close modals on outside click
        window.addEventListener('click', (e) => {
            if (e.target === addItemModal) closeItem();
            if (e.target === stockInModal) closeStockIn();
            if (e.target === stockOutModal) closeStockOut();
            if (e.target === historyModal) closeHistory();
        });
    });
</script>
@endpush