<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Setup | Tanaman</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Add specific styles for the setup badge */
        .setup-badge {
            background-color: #319B72; /* Your theme green */
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .login-card {
            margin-top: 5rem;
            border-top: 5px solid #319B72; /* Green top border accent */
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-brand">
            <div class="logo-container">
                <img src="{{ asset('images/TanamanLogo.png') }}" alt="Logo" class="nav-logo">
            </div>
            <span class="company-name">Tanaman</span>
        </div>
    </nav>

    <div class="login-card">
        <div class="card-header">
            <div class="setup-badge">
                <i class="fas fa-user-shield"></i> System Configuration
            </div>
            
            <h2>Admin Setup</h2>
            <p>Welcome! Please create the <strong>Owner Account</strong> to get started.</p>
        </div>

        <form class="card-body" novalidate>
            <div class="input-group">
                <label for="name">Admin Full Name</label>
                <input type="text" id="name" placeholder="Enter full name" required>
            </div>

            <div class="input-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" placeholder="Create a username" required>
            </div>

            <div class="input-group">
                <label for="email">Admin Email</label>
                <input type="email" id="email" placeholder="admin@company.com" required>
            </div>

            <div class="input-group">
                <label for="password">Master Password</label>
                <input type="password" id="password" placeholder="Min. 6 characters" required>
            </div>

            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="submit-btn" style="margin-top: 1rem;">
                <i class="fas fa-rocket" style="margin-right: 5px;"></i> Complete Setup
            </button>
        </form>

        <div class="card-footer">
            <p style="font-size: 0.85rem; color: #666;">
                <i class="fas fa-lock"></i> This is a one-time setup process.
            </p>
        </div>
    </div>

    <script>
        const registerForm = document.querySelector('.card-body');

        // Helper function for showing errors
        function showError(message) {
            Swal.fire({
                title: 'Validation Error',
                text: message,
                icon: 'warning',
                iconColor: '#d33',
                confirmButtonColor: '#1A4D3F',
                confirmButtonText: 'Okay'
            });
        }

        registerForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            // Get values
            const name = document.getElementById('name').value.trim();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // --- 1. CHECK FOR EMPTY FIELDS ---
            if (!name || !username || !email || !password || !confirmPassword) {
                showError('Please fill in all fields.');
                return;
            }

            // --- 2. VALIDATE EMAIL FORMAT ---
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                showError('Please enter a valid email address.');
                return;
            }

            // --- 3. VALIDATE PASSWORD LENGTH ---
            if (password.length < 6) {
                showError('Password must be at least 6 characters long.');
                return;
            }

            // --- 4. CHECK PASSWORDS MATCH ---
            if (password !== confirmPassword) {
                showError('Passwords do not match.');
                return;
            }

            // --- SUCCESS ---
            const formData = {
                name: name,
                username: username,
                email: email,
                password: password,
                password_confirmation: confirmPassword
            };

            try {
                // Show loading state
                Swal.fire({
                    title: 'Setting up...',
                    text: 'Creating database and admin account',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch("{{ route('setup.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        title: 'Setup Complete!',
                        text: 'Admin account created successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = result.redirect; 
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.message || 'Something went wrong.',
                        icon: 'error'
                    });
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'System Error',
                    text: 'Could not connect to server',
                    icon: 'error'
                });
            }
        });
    </script>

</body>

</html>