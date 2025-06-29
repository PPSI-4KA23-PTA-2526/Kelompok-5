<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="css/auth.css">
    <title>Login - Teh Tarhadi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
</head>

<body>
    <div class="login-container">
        <div class="header-section">
            <img src="{{ asset('image/logo1.png') }}" alt="Logo Teh Tarhadi" class="login-logo">
        </div>


        <div class="form-section">
            <!-- Alert Messages -->
            <div id="alert-container"></div>

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="username">Email</label>
                        <input type="email" id="username" name="email" class="form-control" placeholder="Masukan email anda"
                            value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Enter password" required>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-password">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text">Login</span>
                    <div class="loading"></div>
                </button>

                <div style="text-align: center; margin-top: 30px; font-size: 14px; color: #6b7280;">
                    Belum punya akun?
                    <a href="{{ route('register') }}" style="color: #4b6f32; font-weight: bold; text-decoration: none;">
                        Daftar sekarang
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form Submission with Loading State
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const submitBtn = document.getElementById('loginBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');

            // Show loading state
            btnText.style.display = 'none';
            loading.style.display = 'block';
            submitBtn.disabled = true;

            // In real implementation, remove this setTimeout
            setTimeout(() => {
                btnText.style.display = 'inline-block';
                loading.style.display = 'none';
                submitBtn.disabled = false;
            }, 2000);
        });

        // Show Alert Function
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alert-container');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.innerHTML = message;

            alertContainer.innerHTML = '';
            alertContainer.appendChild(alertDiv);

            // Auto hide after 5 seconds
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => {
                    alertDiv.remove();
                }, 300);
            }, 5000);
        }

        // Handle Laravel Validation Errors
        @if ($errors->any())
            showAlert('{{ $errors->first() }}', 'danger');
        @endif

        // Handle Success Messages
        @if (session('status'))
            showAlert('{{ session('status') }}', 'success');
        @endif

        // Enhanced form validation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function () {
                if (this.value.trim() === '') {
                    this.style.borderBottomColor = '#ef4444';
                } else {
                    this.style.borderBottomColor = '#22c55e';
                }
            });

            input.addEventListener('focus', function () {
                this.style.borderBottomColor = '#22c55e';
            });
        });

        // Email validation
        document.getElementById('username').addEventListener('input', function () {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.style.borderBottomColor = '#ef4444';
            } else if (this.value) {
                this.style.borderBottomColor = '#22c55e';
            }
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>