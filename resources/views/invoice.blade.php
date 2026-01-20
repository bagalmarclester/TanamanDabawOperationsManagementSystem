@extends('layouts.app')

@section('title', 'Invoice | Tanaman')

@section('content')


<div class="page-header">
    <div>
        <h2>Invoices</h2>
        <p>View and manage invoices </p>
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
                <th>Client</th>
                <th>Total</th>
                <th>Created</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Acme Corp</td>
                <td>₱1,200.00</td>
                <td>2023-10-01</td>
                <td>2023-10-15</td>
                <td>Paid</td>
                <td><button class="btn-action"><i class="fas fa-eye"></i></button></td>
            </tr>
        </tbody>
    </table>
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
</script>
@endpush