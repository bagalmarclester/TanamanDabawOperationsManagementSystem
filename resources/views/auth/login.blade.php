<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login | Tanaman</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h2>Welcome Back</h2>
            <p>Enter your credentials to access the dashboard</p>
        </div>

        <form class="card-body" id="loginForm">
            <div class="input-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="email" placeholder="Enter username or email" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="actions">
                <label for="remember-me" class="remember-me">
                    <input type="checkbox" id="remember-me" name="remember">
                    Remember me
                </label>
                <a href="#" class="forgot-password">Forgot password?</a>
            </div>

            <button type="submit" class="submit-btn">Sign In</button>
        </form>

        <div class="card-footer">
            <p>Don't have an account? <a href="#">Contact Admin</a></p>
        </div>

    </div>

    <script>
        const loginForm = document.getElementById('loginForm');

        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault(); 
            
            const usernameInput = document.getElementById('username').value;
            const passwordInput = document.getElementById('password').value;
            const rememberInput = document.getElementById('remember-me').checked;

            try {
                // 2. Fetch Request to Laravel Route
                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // Include the CSRF token from the meta tag
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        // We send 'email' key because the controller expects $request->only('email', 'password')
                        // This works even if the user types a username, based on our previous controller logic.
                        login: usernameInput, 
                        password: passwordInput,
                        remember: rememberInput
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        title: 'Welcome Back!',
                        text: `${result.message}`,
                        icon: 'success',
                        iconColor: '#319B72',       
                        confirmButtonColor: '#1A4D3F', 
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to the dashboard route
                        // Assuming you have a route named 'dashboard' or 'home'
                        window.location.href = "{{ route('dashboard') }}";
                    });

                } else {
                    // Handle Validation Errors or Bad Credentials
                    let errorMessage = result.message || 'Invalid username or password.';
                    
                    Swal.fire({
                        title: 'Access Denied',
                        text: errorMessage,
                        icon: 'error',
                        iconColor: '#d33',          
                        confirmButtonColor: '#1A4D3F', 
                        confirmButtonText: 'Try Again'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'System Error',
                    text: 'Something went wrong. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#1A4D3F'
                });
            }
        });
    </script>

</body>
</html>