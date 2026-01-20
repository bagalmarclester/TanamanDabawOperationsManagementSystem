@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('content')

<header class="content-header">
    <h2>Dashboard</h2>
    <p>Welcome back, here's what's happening today.</p>
</header>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-text">
            <h3>Total Clients</h3>
            <p class="number">{{ $totalClients }}</p>
            <a href="{{ route('clients') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-blue">
            <i class="fas fa-user-friends"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-text">
            <h3>Active Projects</h3>
            <p class="number">{{ $totalActiveProjects }}</p>
            <a href="{{ route('projects') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-green">
            <i class="fas fa-briefcase"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-text">
            <h3>Employees</h3>
            <p class="number">{{ $totalEmployees }}</p>
            <a href="employee.html" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-purple">
            <i class="fas fa-user-check"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-text">
            <h3>Quotes Sent</h3>
            <p class="number">0</p>
            <a href="quote.html" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-orange">
            <i class="fas fa-file-alt"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-text">
            <h3>Invoices Issued</h3>
            <p class="number">0</p>
            <a href="invoice.html" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-teal">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-text">
            <h3>Inventory Items</h3>
            <p class="number">0</p>
            <a href="inventory.html" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-red">
            <i class="fas fa-boxes"></i>
        </div>
    </div>
</div>

<h3 class="section-title">Quick Actions</h3>

<div class="quick-actions-grid">
    <button class="action-card" id="addClientBtn">
        <div class="action-icon icon-bg-blue">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="action-text">
            <span class="action-title">Add Client</span>
            <span class="action-desc">Register new customer</span>
        </div>
    </button>

    <button class="action-card" id="addProjectBtn">
        <div class="action-icon icon-bg-green">
            <i class="fas fa-briefcase"></i>
        </div>
        <div class="action-text">
            <span class="action-title">New Project</span>
            <span class="action-desc">Create & track job</span>
        </div>
    </button>

    <button class="action-card" id="addEmployeeBtn">
        <div class="action-icon icon-bg-purple">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="action-text">
            <span class="action-title">Add Employee</span>
            <span class="action-desc">Onboard team member</span>
        </div>
    </button>

    <button class="action-card" id="createQuoteBtn">
        <div class="action-icon icon-bg-orange">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="action-text">
            <span class="action-title">Create Quote</span>
            <span class="action-desc">Estimate for client</span>
        </div>
    </button>

    <button class="action-card" id="createInvoiceBtn">
        <div class="action-icon icon-bg-teal">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="action-text">
            <span class="action-title">New Invoice</span>
            <span class="action-desc">Bill completed work</span>
        </div>
    </button>

    <button class="action-card" id="addItemBtn">
        <div class="action-icon icon-bg-red">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="action-text">
            <span class="action-title">Add Item</span>
            <span class="action-desc">Update stock levels</span>
        </div>
    </button>
</div>


<div class="modal-overlay" id="clientModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add New Client</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <form class="modal-form" id="clientForm">
            <div class="input-group">
                <label>Client Name</label>
                <input type="text" id="client_name" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="client_email" required>
            </div>
            <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" id="client_phone">
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" id="client_address">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Client</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="employeeModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add New Employee</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <form class="modal-form" id="employeeForm">
            <div class="input-group">
                <label>Employee Name</label>
                <input type="text" id="emp_name" placeholder="e.g. John Doe" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="emp_email" placeholder="contact@company.com" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Employee</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="addProjectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add New Project</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <form class="modal-form" id="projectForm">
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
                <button type="submit" class="btn-save">Save Project</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="addQuoteModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Create New Quote</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <form class="modal-form" id="quoteForm">
            <div class="input-group">
                <label>Client Name</label>
                <input type="text" placeholder="Search client..." required>
            </div>
            <div class="input-group">
                <label>Valid Until</label>
                <input type="date" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Create Quote</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="addInvoiceModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>New Invoice</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <form class="modal-form" id="invoiceForm">
            <div class="input-group">
                <label>Invoice Number</label>
                <input type="text" value="#INV-{{ rand(1000,9999) }}" readonly style="background-color: #f3f4f6;">
            </div>
            <div class="input-group">
                <label>Client</label>
                <input type="text" placeholder="Client Name" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Generate Invoice</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="addItemModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Inventory Item</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <form class="modal-form" id="inventoryForm">
            <div class="input-group">
                <label>Item Name</label>
                <input type="text" placeholder="e.g. Fertilizer" required>
            </div>
            <div class="input-group">
                <label>Quantity</label>
                <input type="number" placeholder="0" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('p_client')) {
            new TomSelect("#p_client", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Select a client...',
                maxItems: 1
            });
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const modalMap = {
            'addClientBtn': 'clientModal',
            'addEmployeeBtn': 'employeeModal',
            'addProjectBtn': 'addProjectModal',
            'addQuoteBtn': 'addQuoteModal',
            'addInvoiceBtn': 'addInvoiceModal',
            'addItemBtn': 'addItemModal'
        };


        const openModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'flex';
        };

        const closeModal = (modal) => {
            modal.style.display = 'none';
            const form = modal.querySelector('form');
            if (form) form.reset(); // Clear inputs on close
        };

        // Attach Click Events to Buttons
        Object.keys(modalMap).forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault(); // Stop generic button behavior
                    openModal(modalMap[btnId]);
                });
            }
        });

        // Attach Close Events (X button, Cancel button, Outside Click)
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            const closeBtn = modal.querySelector('.close-modal-btn');
            const cancelBtn = modal.querySelector('.btn-cancel');

            if (closeBtn) closeBtn.addEventListener('click', () => closeModal(modal));
            if (cancelBtn) cancelBtn.addEventListener('click', () => closeModal(modal));

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        });


        const clientForm = document.getElementById('clientForm');
        if (clientForm) {
            clientForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = {
                    name: document.getElementById('client_name').value,
                    email: document.getElementById('client_email').value,
                    phone: document.getElementById('client_phone').value,
                    address: document.getElementById('client_address').value
                };
                await sendData("{{ route('clients.create') }}", formData, 'clientModal');
            });
        }

        const empForm = document.getElementById('employeeForm');
        if (empForm) {
            empForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = {
                    name: document.getElementById('emp_name').value,
                    email: document.getElementById('emp_email').value,
                    position: 'Employee',
                };
                await sendData("{{ route('employees.store') }}", formData, 'employeeModal');
            });
        }

        const projForm = document.getElementById('projectForm');
        if (projForm) {
            projForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = {
                    project_name: document.getElementById('p_name').value,
                    client_id: document.getElementById('p_client').value,
                    project_end_date: document.getElementById('p_deadline').value,
                    project_budget: document.getElementById('p_budget').value
                };
                await sendData("{{ route('projects.create') }}", formData, 'addProjectModal');
            });
        }


        ['quoteForm', 'invoiceForm', 'inventoryForm'].forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    // Close the modal visually
                    form.closest('.modal-overlay').style.display = 'none';

                    Swal.fire({
                        title: 'Coming Soon',
                        text: 'This feature is not yet connected to the database.',
                        icon: 'info',
                        confirmButtonColor: '#319B72'
                    });
                });
            }
        });

        async function sendData(url, data, modalId) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    document.getElementById(modalId).style.display = 'none';
                    Swal.fire({
                        title: 'Success!',
                        text: result.message || 'Saved successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
                } else {
                    let errorMsg = result.message || 'Validation Failed';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    Swal.fire('Error', errorMsg, 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'System error occurred', 'error');
            }
        }
    });
</script>
@endpush