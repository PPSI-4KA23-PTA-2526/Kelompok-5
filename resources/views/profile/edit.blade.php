<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Teh Tarhadi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f9fafb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .topbar {
            background: #BC5DFF;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .topbar img {
            max-height: 40px;
        }

        .topbar .right-menu {
            display: flex;
            align-items: center;
            gap: 20px;
            color: white;
            position: relative;
        }

        .right-menu a {
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .right-menu a:hover {
            opacity: 0.8;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 45px;
            right: 0;
            background: white;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 999;
            min-width: 150px;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            background: none;
            color: #BC5DFF;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background-color: #f3f4f6;
        }

        /* Main content area with top margin to account for fixed topbar */
        .main-content {
            margin-top: 80px; /* Adjust based on topbar height */
            padding: 20px 0;
        }

        .login-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 40px 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section h2 {
            font-size: 20px;
            color: #BC5DFF;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #BC5DFF;
            box-shadow: 0 0 0 3px rgba(75, 111, 50, 0.1);
        }

        .btn-login {
            background: #BC5DFF;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        .btn-login:hover {
            background: #8aad52;
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-login .loading {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }

        .delete-btn {
            background-color: #dc2626;
        }

        .delete-btn:hover {
            background-color: #b91c1c;
        }

        .dropdown-btn {
            background: none;
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.3s;
        }

        .dropdown-btn:hover {
            opacity: 0.8;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .topbar {
                padding: 15px 20px;
            }
            
            .login-container {
                margin: 0 20px;
                padding: 30px 25px;
            }
            
            .right-menu {
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Topbar -->
    <div class="topbar">
        <a href="/">
            <img src="{{ asset('image/logo1.png') }}" alt="Logo Teh Tarhadi">
        </a>
        <div class="right-menu">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown()">
                    {{ Auth::user()->name }} 
                    <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                </button>
                <div id="dropdownMenu" class="dropdown-menu">
                    <a href="{{ route('profile.edit') }}">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="login-container">
            {{-- Update Profile --}}
            <div class="form-section">
                <h2>Edit Informasi Profil</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="form-group">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="btn-text">Perbarui Profil</span>
                        <div class="loading"></div>
                    </button>
                </form>
            </div>

            {{-- Ganti Password --}}
            <div class="form-section">
                <h2>Ganti Password</h2>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label class="form-label" for="current_password">Password Lama</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password Baru</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="btn-text">Ubah Password</span>
                        <div class="loading"></div>
                    </button>
                </form>
            </div>

            {{-- Hapus Akun --}}
            <div class="form-section">
                <h2>Hapus Akun</h2>
                <p style="color: #dc2626; margin-bottom: 20px; font-size: 14px;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Peringatan: Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.
                </p>
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteForm">
                    @csrf
                    @method('delete')
                    
                    <div class="form-group">
                        <label class="form-label" for="password_delete">Konfirmasi Password</label>
                        <input type="password" id="password_delete" name="password" class="form-control" 
                               placeholder="Masukkan password Anda untuk konfirmasi" required>
                        @if ($errors->userDeletion->has('password'))
                            <div style="color: #dc2626; font-size: 14px; margin-top: 5px;">
                                {{ $errors->userDeletion->first('password') }}
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" class="btn-login delete-btn" onclick="handleDelete()">
                        <span class="btn-text">Hapus Akun</span>
                        <div class="loading"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function confirmDelete() {
            return confirm('Yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan dan semua data akan hilang permanen.');
        }

        function handleDelete() {
            const passwordInput = document.getElementById('password_delete');
            if (!passwordInput.value) {
                alert('Silakan masukkan password untuk konfirmasi.');
                passwordInput.focus();
                return;
            }

            if (confirm('Yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan dan semua data akan hilang permanen.')) {
                const form = document.getElementById('deleteForm');
                const btn = event.target;
                const btnText = btn.querySelector('.btn-text');
                const loading = btn.querySelector('.loading');
                
                btnText.style.display = 'none';
                loading.style.display = 'inline-block';
                btn.disabled = true;
                
                form.submit();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('dropdownMenu');
            if (!e.target.closest('.dropdown')) {
                dropdown.style.display = 'none';
            }
        });

        // Loading state for update forms only (not delete form)
        document.querySelectorAll('form:not(#deleteForm)').forEach(form => {
            const btn = form.querySelector('button[type="submit"]');
            const btnText = btn?.querySelector('.btn-text');
            const loading = btn?.querySelector('.loading');
            
            if (btn && btnText && loading) {
                form.addEventListener('submit', function() {
                    btnText.style.display = 'none';
                    loading.style.display = 'inline-block';
                    btn.disabled = true;
                });
            }
        });

        // Handle form validation errors (if any)
        window.addEventListener('load', function() {
            const errors = document.querySelectorAll('.error-message');
            if (errors.length > 0) {
                // Re-enable buttons if there are validation errors
                document.querySelectorAll('button[type="submit"]').forEach(btn => {
                    const btnText = btn.querySelector('.btn-text');
                    const loading = btn.querySelector('.loading');
                    if (btnText && loading) {
                        btnText.style.display = 'inline';
                        loading.style.display = 'none';
                        btn.disabled = false;
                    }
                });
            }
        });
    </script>
</body>

</html>