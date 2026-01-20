<nav class="navbar">
    <i class="fas fa-bars" id="menu-toggle"
        style="font-size: 1.5rem; cursor: pointer; display: none; margin-right: 15px;"></i>

    <div class="nav-brand">
        <div class="logo-container">
            <img src="{{ asset('images/TanamanLogo.png') }}" alt="Logo" class="nav-logo">
        </div>
        <span class="company-name">Tanaman</span>
    </div>

    <div class="nav-profile" id="profile-trigger">
        <div class="profile-text">
            <span class="profile-name">{{ Auth::user()->name }}</span>
            <span class="profile-role">{{ Auth::user()->is_admin ? 'Admin' : 'User' }}</span>
        </div>
        <img src="{{ asset('images/images.jpg') }}" class="profile-pic">
        <i class="fas fa-chevron-down dropdown-icon"></i>

        <div class="dropdown-menu" id="profile-dropdown">
            <div class="dropdown-header">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-email">{{ Auth::user()->email }}</div>
            </div>
            <hr>
            <a href="{{ route('profile') }}" class="dropdown-item">
                <i class="far fa-user"></i> My Profile
            </a>
            <a href="#" class="dropdown-item">
                <i class="fas fa-cog"></i> Account Setting
            </a>
            <hr>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item logout-item" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileTrigger = document.getElementById('profile-trigger');
        const dropdownMenu = document.getElementById('profile-dropdown');
        const dropdownIcon = document.querySelector('.dropdown-icon');

        if (profileTrigger && dropdownMenu) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');

                if (dropdownIcon) {
                    dropdownIcon.style.transform = dropdownMenu.classList.contains('show') ?
                        'rotate(180deg)' :
                        'rotate(0deg)';
                }
            });

            window.addEventListener('click', function(e) {
                if (!profileTrigger.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                    if (dropdownIcon) {
                        dropdownIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });
        }
    });
</script>
@endpush