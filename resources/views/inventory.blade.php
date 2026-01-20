@extends('layouts.app')

@section('title', 'Inventory | Tanaman')

@section('content')


<div class="page-header">
    <div>
        <h2>Inventory</h2>
        <p>Stock levels and product catalog</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div style="position: relative;">
            <input type="text" id="searchInput" placeholder="Search inventory..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </div>
        <button class="btn-primary" id="addItemBtn">
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
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
        <tbody>
            <tr>
                <td>
                    <div style="font-weight: 600; color: #334155;">Office Desk</div>
                </td>
                <td>
                    <div style="color: #64748b; font-weight: 500;">FUR-001</div>
                </td>
                <td>Furniture</td>
                <td>₱299.00</td>
                <td><span style="font-weight: 700; font-size: 1.1rem; color: #334155;">15</span></td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-stock-in" style="background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="font-size: 0.7rem;"></i> In
                        </button>
                        <button class="btn-stock-out" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-down" style="font-size: 0.7rem;"></i> Out
                        </button>
                    </div>
                </td>
                <td>
                    <button class="action-btn"><i class="fas fa-pen"></i></button>
                    <button class="action-btn delete"><i class="far fa-trash-alt"></i></button>
                </td>
            </tr>

            <tr>
                <td>
                    <div style="font-weight: 600; color: #334155;">Ergo Chair</div>
                </td>
                <td>
                    <div style="color: #64748b; font-weight: 500;">FUR-002</div>
                </td>
                <td>Furniture</td>
                <td>₱150.00</td>
                <td><span style="font-weight: 700; font-size: 1.1rem; color: #ef4444;">2</span></td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-stock-in" style="background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="font-size: 0.7rem;"></i> In
                        </button>
                        <button class="btn-stock-out" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-down" style="font-size: 0.7rem;"></i> Out
                        </button>
                    </div>
                </td>
                <td>
                    <button class="action-btn"><i class="fas fa-pen"></i></button>
                    <button class="action-btn delete"><i class="far fa-trash-alt"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-overlay" id="addItemModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add New Item</h3>
            <span class="close-modal-btn" id="closeAddItem">&times;</span>
        </div>

        <form class="modal-form">
            <div class="input-group">
                <label>Item Name</label>
                <input type="text" placeholder="e.g. Office Chair" required>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>SKU</label>
                    <input type="text" placeholder="e.g. FUR-001">
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Category</label>
                    <select style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                        <option>Furniture</option>
                        <option>Electronics</option>
                        <option>Office Supplies</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>Price (₱)</label>
                    <input type="number" step="0.01" placeholder="0.00">
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Initial Stock</label>
                    <input type="number" placeholder="0">
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
                <div class="header-icon-box bg-green">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <h3 style="margin: 0; color: #064e3b;">Stock In</h3>
            </div>
            <span class="close-modal-btn" id="closeStockIn">&times;</span>
        </div>

        <div style="padding: 0 20px 20px 20px;">
            <div class="stock-info-card">
                <div>
                    <label>CURRENT STOCK</label>
                    <div class="stock-value" id="modalCurrentStock">15</div>
                </div>
                <div style="text-align: right;">
                    <label>ITEM SKU</label>
                    <div class="sku-value" id="modalSku">FUR-001</div>
                </div>
            </div>

            <form class="stock-form">
                <div class="input-group">
                    <label>QUANTITY TO ADD</label>
                    <input type="number" class="dark-input" placeholder="0">
                </div>
                <div class="input-group">
                    <label>REASON / REFERENCE</label>
                    <div class="input-with-icon">
                        <i class="far fa-clipboard" style="color: #94a3b8; left: 12px;"></i>
                        <input type="text" class="dark-input" placeholder="e.g. New Shipment #1234" style="padding-left: 40px;">
                    </div>
                </div>
                <div class="modal-actions" style="margin-top: 25px; align-items: center;">
                    <button type="button" class="btn-cancel" id="cancelStockIn" style="border:none; background:none;">Cancel</button>
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
                <div class="header-icon-box bg-red-soft">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <h3 style="margin: 0; color: #7f1d1d;">Stock Out</h3>
            </div>
            <span class="close-modal-btn" id="closeStockOut">&times;</span>
        </div>

        <div style="padding: 0 20px 20px 20px;">
            <div class="stock-info-card">
                <div>
                    <label>CURRENT STOCK</label>
                    <div class="stock-value" id="outModalStock">0</div>
                </div>
                <div style="text-align: right;">
                    <label>ITEM SKU</label>
                    <div class="sku-value" id="outModalSku">---</div>
                </div>
            </div>

            <form class="stock-form">
                <div class="input-group">
                    <label>QUANTITY TO REMOVE</label>
                    <input type="number" class="dark-input input-red-focus" placeholder="0">
                </div>
                <div class="input-group">
                    <label>REASON / REFERENCE</label>
                    <div class="input-with-icon">
                        <i class="far fa-clipboard" style="color: #94a3b8; left: 12px;"></i>
                        <input type="text" class="dark-input input-red-focus" placeholder="e.g. Sales Order #9988" style="padding-left: 40px;">
                    </div>
                </div>
                <div class="modal-actions" style="margin-top: 25px; align-items: center;">
                    <button type="button" class="btn-cancel" id="cancelStockOut" style="border:none; background:none;">Cancel</button>
                    <button type="submit" class="btn-save btn-red">Confirm Stock Out</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const searchInput = document.getElementById('searchInput');
        const table = document.querySelector('.data-table');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');

                for (let i = 1; i < rows.length; i++) {
                    let textContent = rows[i].innerText.toLowerCase();
                    if (textContent.includes(filter)) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            });
        }

        const profileTrigger = document.getElementById('profile-trigger');
        const dropdownMenu = document.getElementById('profile-dropdown');
        const dropdownIcon = document.querySelector('.dropdown-icon');

        if (profileTrigger && dropdownMenu) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
                if (dropdownIcon) {
                    dropdownIcon.style.transform = dropdownMenu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            });

            window.addEventListener('click', function(e) {
                if (!dropdownMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                    if (dropdownIcon) dropdownIcon.style.transform = 'rotate(0deg)';
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
                if (sidebar.classList.contains('active') &&
                    !sidebar.contains(e.target) &&
                    e.target !== menuToggle) {
                    sidebar.classList.remove('active');
                }
            });
        }

        const addItemModal = document.getElementById('addItemModal');
        const openAddItem = document.getElementById('addItemBtn');
        const closeAddItem = document.getElementById('closeAddItem');
        const cancelAddItem = document.getElementById('cancelAddItem');

        if (openAddItem) openAddItem.addEventListener('click', () => addItemModal.style.display = 'flex');
        if (closeAddItem) closeAddItem.addEventListener('click', () => addItemModal.style.display = 'none');
        if (cancelAddItem) cancelAddItem.addEventListener('click', () => addItemModal.style.display = 'none');


        const stockInModal = document.getElementById('stockInModal');
        const closeStockIn = document.getElementById('closeStockIn');
        const cancelStockIn = document.getElementById('cancelStockIn');
        const stockInButtons = document.querySelectorAll('.btn-stock-in');
        const modalSku = document.getElementById('modalSku');
        const modalCurrentStock = document.getElementById('modalCurrentStock');

        stockInButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const row = button.closest('tr');
                const skuText = row.cells[1].innerText;
                const stockText = row.cells[4].innerText;
                modalSku.innerText = skuText;
                modalCurrentStock.innerText = stockText;
                stockInModal.style.display = 'flex';
            });
        });

        if (closeStockIn) closeStockIn.addEventListener('click', () => stockInModal.style.display = 'none');
        if (cancelStockIn) cancelStockIn.addEventListener('click', () => stockInModal.style.display = 'none');


        const stockOutModal = document.getElementById('stockOutModal');
        const closeStockOut = document.getElementById('closeStockOut');
        const cancelStockOut = document.getElementById('cancelStockOut');
        const stockOutButtons = document.querySelectorAll('.btn-stock-out');
        const outModalSku = document.getElementById('outModalSku');
        const outModalStock = document.getElementById('outModalStock');

        stockOutButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const row = button.closest('tr');
                const skuText = row.cells[1].innerText;
                const stockText = row.cells[4].innerText;
                outModalSku.innerText = skuText;
                outModalStock.innerText = stockText;
                stockOutModal.style.display = 'flex';
            });
        });

        if (closeStockOut) closeStockOut.addEventListener('click', () => stockOutModal.style.display = 'none');
        if (cancelStockOut) cancelStockOut.addEventListener('click', () => stockOutModal.style.display = 'none');


        window.addEventListener('click', (e) => {
            if (e.target === addItemModal) addItemModal.style.display = 'none';
            if (e.target === stockInModal) stockInModal.style.display = 'none';
            if (e.target === stockOutModal) stockOutModal.style.display = 'none';
        });
    });
</script>
@endpush