<aside class="sidebar">
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('clients') }}"
                class="{{ request()->routeIs('clients*') ? 'active' : '' }}">
                <i class="fas fa-user-friends"></i> Clients
            </a>
        </li>

        <li>
            <a href="{{ route('projects') }}"
                class="{{ request()->routeIs('projects*') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i> Projects
            </a>
        </li>

        <li>
            <a href="{{ route('employees') }}"
                class="{{ request()->routeIs('employees*') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i> Employees
            </a>
        </li>

        <li>
            <a href="{{ route('quotes') }}"
                class="{{ request()->routeIs('quotes*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Quotes
            </a>
        </li>

        <li>
            <a href="{{ route('invoices') }}"
                class="{{ request()->routeIs('invoices*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Invoices
            </a>
        </li>

        <li>
            <a href="{{ route('inventory') }}"
                class="{{ request()->routeIs('inventory*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Inventory
            </a>
        </li>
    </ul>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <div class="sidebar-footer">
            <button type="submit" class="sign-out-link" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer; font-size: 1rem;">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </button>
        </div>
    </form>
</aside>