@extends('layouts.app')

@section('title', 'Quotes | Tanaman')


@section('content')

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
                <th>Client</th>
                <th>Total</th>
                <th>Created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>BET Solutions</td>
                <td>₱ 15,000.00</td>
                <td>2024-06-15</td>
                <td><span class="status-badge quote-pending">Pending</span></td>
                <td>
                    <button class="btn-action"><i class="fas fa-eye"></i></button>
                    <button class="btn-action"><i class="fas fa-edit"></i></button>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-overlay" id="addQuoteModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Create New Quote</h3>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form class="modal-form">
            <div class="input-group">
                <label>Client Name</label>
                <input type="text" placeholder="Client Name" required>
            </div>
            <div class="input-group">
                <label>Quote Date</label>
                <input type="date" id="quoteDate" required readonly>
            </div>
            <div class="input-group">
                <label>Valid Until</label>
                <input type="date" required>
            </div>

            <div class="form-group">
                <div class="line-items-header">
                    <label>Line Items</label>
                    <span class="add-item-link" id="addItemBtn">+ Add item</span>
                </div>
                <div id="itemsContainer"></div>
            </div>
            <div class="input-group" style="margin-top: 15px;">
                <label>Total Amount (₱)</label>
                <input type="number" placeholder="0.00" step="0.01" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Create Quote</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    const modal = document.getElementById('addQuoteModal');
    const openBtn = document.getElementById('addQuoteBtn');
    const closeBtn = document.querySelector('.close-modal-btn');
    const cancelBtn = document.querySelector('.btn-cancel');
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');


    function createItemRow() {
        const div = document.createElement('div');
        div.classList.add('item-row');
        div.innerHTML = `
                <input type="text" class="input-desc" placeholder="Description" required>
                <input type="number" class="input-small" placeholder="Qty" required>
                <input type="number" class="input-small" placeholder="Price" step="0.01" required>
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                    <i class="far fa-trash-alt"></i>
                </button>
            `;
        itemsContainer.appendChild(div);
    }

    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const dateInput = document.getElementById('quoteDate');
            if (dateInput) dateInput.value = `${year}-${month}-${day}`;
            itemsContainer.innerHTML = '';
            createItemRow();
        });
    }


    if (closeBtn) closeBtn.addEventListener('click', () => modal.style.display = 'none');
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.style.display = 'none');

    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    if (addItemBtn) {
        addItemBtn.addEventListener('click', createItemRow);
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
</script>
@endpush