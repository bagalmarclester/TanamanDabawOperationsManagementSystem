@extends('layouts.app')

@section('title', 'Profile | Tanaman')

@section('content')

<div class="page-header">
    <div>
        <h2>My Profile</h2>
        <p>Manage your personal information</p>
    </div>
</div>

<div class="profile-card">
    <div class="profile-banner"></div>

    <div class="profile-body">
        <div class="profile-header-row">
            <div class="profile-avatar-wrapper">
                <img src="{{ asset('images/images.jpg') }}" alt="Marc" class="profile-avatar-large">
                <div class="camera-btn">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <button class="btn-primary" id="editProfileBtn" style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                Edit Profile
            </button>
        </div>

        <form class="profile-form-grid" id="profileForm">

            <div class="input-group">
                <label>FULL NAME</label>
                <div class="input-with-icon">
                    <i class="far fa-user" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
                    <input type="text" value="Marc Lester" readonly style="padding-left: 40px;">
                </div>
            </div>

            <div class="input-group">
                <label>EMAIL ADDRESS</label>
                <div class="input-with-icon">
                    <i class="far fa-envelope" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
                    <input type="email" value="marclester@gmail.com" readonly style="padding-left: 40px;">
                </div>
            </div>

            <div class="input-group">
                <label>PHONE</label>
                <div class="input-with-icon">
                    <i class="fas fa-phone-alt" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
                    <input type="tel" value="+1 (555) 000-0000" readonly style="padding-left: 40px;">
                </div>
            </div>

            <div class="input-group">
                <label>LOCATION</label>
                <div class="input-with-icon">
                    <i class="fas fa-map-marker-alt" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
                    <input type="text" value="New York, USA" readonly style="padding-left: 40px;">
                </div>
            </div>

            <div class="input-group full-width">
                <label>BIO</label>
                <textarea rows="4" readonly style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #334155;">Senior Administrator with 10+ years of experience in business management. Passionate about streamlining operations and improving team efficiency.</textarea>
            </div>

        </form>
    </div>
</div>


@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', () => {
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

        const editBtn = document.getElementById('editProfileBtn');
        const formInputs = document.querySelectorAll('.profile-form-grid input, .profile-form-grid textarea');
        let isEditing = false;

        if (editBtn) {
            editBtn.addEventListener('click', (e) => {
                e.preventDefault();

                if (!isEditing) {
                    isEditing = true;
                    formInputs.forEach(input => input.removeAttribute('readonly'));
                    editBtn.textContent = "Save Changes";
                    editBtn.style.backgroundColor = "#1A4D3E";
                    editBtn.style.color = "white";
                    editBtn.style.borderColor = "#1A4D3E";
                    formInputs[0].focus();

                } else {

                    isEditing = false;
                    formInputs.forEach(input => input.setAttribute('readonly', true));


                    editBtn.textContent = "Edit Profile";
                    editBtn.style.backgroundColor = "#f1f5f9";
                    editBtn.style.color = "#334155";
                    editBtn.style.borderColor = "#cbd5e1";


                    Swal.fire({
                        title: 'Profile Updated!',
                        text: 'Your personal information has been saved.',
                        icon: 'success',
                        confirmButtonColor: '#319B72',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

    });
</script>
@endpush