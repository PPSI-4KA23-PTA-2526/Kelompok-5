<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teh Solo Kakek Tarhadi - Warisan Rasa Asli Solo</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-K7aO5wmtnKpu8KaH"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body data-logged-in="{{ auth()->check() ? 'true' : 'false' }}" data-user-id="{{ auth()->id() ?? 'guest' }}">

    <!-- ====== Bagian Navbar ====== -->
    <header>
        <div class="container">

            <!-- Logo di kiri -->
            <a href="/" class="logo">
                <img src="image/logo1.png" alt="Logo Teh Solo Kakek Tarhadi" width="200">
            </a>

            <!-- Navigasi di tengah -->
            <nav id="navMenu">
                <ul>
                    <li><a href="/">Beranda</a></li>
                    <li><a href="/produk">Produk</a></li>
                    <li><a href="/cerita">Cerita Kami</a></li>
                    <li><a href="/proses">Proses Pembuatan</a></li>
                    <li><a href="/kontak">Kontak</a></li>
                </ul>
            </nav>

            <!-- Ikon di kanan -->
            <div class="right-icons">

                <!-- Profile -->
                <div class="profile-icon" id="profileIcon">
                    <i class="fas fa-user"></i>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="profile-menu">
                            @auth
                                <a class="profile-link" style="pointer-events: none; cursor: default;">
                                    <i class="fas fa-user"></i> {{ auth()->user()->name }}
                                </a>
                                <a href="/profile" class="profile-link">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <a href="{{ route('user.orders.index') }}" class="profile-link">
                                    <i class="fa fa-shopping-cart"></i> Status pesanan
                                </a>
                                <a href="{{ route('logout') }}" class="profile-link"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="/admin" class="profile-link">
                                        <i class="fas fa-user-shield"></i> Admin Panel
                                    </a>
                                    <a href="https://dashboard.sandbox.midtrans.com/beta/transactions?start_created_at=2025-06-13T00%3A00%3A00%2B07%3A00&end_created_at=2025-07-14T23%3A59%3A59%2B07%3A00"
                                        class="profile-link">
                                        <i class="fas fa-user-shield"></i> Admin Payment
                                    </a>
                                @endif
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @endauth

                            @guest
                                <a href="{{ route('login') }}" class="profile-link">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                                <a href="{{ route('register') }}" class="profile-link">
                                    <i class="fas fa-user-plus"></i> Register
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>

                <!-- Keranjang -->
                <div class="cart-icon" id="cartIcon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                    <div class="cart-dropdown hidden" id="cartDropdown">
                        <div class="cart-empty" id="cartEmpty">
                            <i class="fas fa-shopping-basket"></i>
                            <p>Keranjang belanja Anda kosong</p>
                        </div>
                        <div class="cart-items hidden" id="cartItems"></div>
                        <div class="cart-footer hidden" id="cartFooter">
                            <div class="cart-total">Total: <span id="cartTotal">Rp 0</span></div>
                            <button class="checkout-btn" id="checkoutBtn">Checkout</button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Hamburger -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>

            </div>

        </div>
    </header>



    <main>
        @yield('content')
    </main>

    <!-- ====== Bagian Footer ====== -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2>Teh Solo Kakek Tarhadi</h2>
                    <p>Berdiri Sejak 2023</p>
                </div>
                <div class="footer-links">
                    <h3>Tautan Cepat</h3>
                    <ul>
                        <li><a href="/">Beranda</a></li>
                        <li><a href="/produk">Produk</a></li>
                        <li><a href="/cerita">Cerita Kami</a></li>
                        <li><a href="/proses">Proses Pembuatan</a></li>
                        <li><a href="/kontak">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Ikuti Kami</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Teh Solo Kakek Tarhadi. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const navMenu = document.getElementById('navMenu');

            mobileMenuToggle.addEventListener('click', function () {
                navMenu.classList.toggle('mobile-menu-open');
                this.classList.toggle('active');

                const icon = this.querySelector('i');
                if (navMenu.classList.contains('mobile-menu-open')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    </script>

</body>

</html>