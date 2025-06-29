<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Teh Tarhadi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}"> {{-- Gunakan file CSS eksternal yang kamu buat --}}
</head>

<body>
    <div class="login-container">
        <div class="header-section">
            <img src="{{ asset('image/logo1.png') }}" alt="Logo Teh Tarhadi" class="login-logo"
                style="max-width: 150px;">
        </div>

        <div class="form-section">
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Nama lengkap"
                            value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email aktif"
                            value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="password_confirmation">Konfirmasi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="registerBtn">
                    <span class="btn-text">Daftar</span>
                    <div class="loading"></div>
                </button>

                <div style="text-align: center; margin-top: 30px; font-size: 14px; color: #6b7280;">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" style="color: #4b6f32; font-weight: bold; text-decoration: none;">
                        Masuk sekarang
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('registerBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const loading = submitBtn.querySelector('.loading');

        form.addEventListener('submit', function () {
            // Show loading
            btnText.style.display = 'none';
            loading.style.display = 'block';
            submitBtn.disabled = true;

            // Jangan pakai setTimeout atau preventDefault
            // biarkan Laravel menangani submit form
        });
    </script>
</body>

</html>