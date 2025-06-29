<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Teh Tarhadi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
    <div class="login-container">
        <div class="header-section">
            <img src="{{ asset('image/logo1.png') }}" alt="Logo Teh Tarhadi" class="login-logo"
                style="max-width: 140px;">
        </div>

        <div class="form-section">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <p class="text-sm text-gray-600 mb-5">
                    Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password Anda.
                </p>

                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email"
                            value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="resetBtn">
                    <span class="btn-text">Kirim Link Reset</span>
                    <div class="loading"></div>
                </button>

                <div style="text-align: center; margin-top: 30px; font-size: 14px; color: #6b7280;">
                    Ingat password?
                    <a href="{{ route('login') }}" style="color: #4b6f32; font-weight: bold; text-decoration: none;">
                        Kembali login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('resetBtn');
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