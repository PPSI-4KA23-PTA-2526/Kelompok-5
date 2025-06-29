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
            <a href="/">
                <img src="image/logo1.png" alt="Logo Teh Solo Kakek Tarhadi" width="200">
            </a>

            <nav>
                <ul>
                    <li><a href="/">Beranda</a></li>
                    <li><a href="/produk">Produk</a></li>
                    <li><a href="/cerita">Cerita Kami</a></li>
                    <li><a href="/proses">Proses Pembuatan</a></li>
                    <li><a href="/kontak">Kontak</a></li>

                    <!-- Profile -->
                    <li class="profile-icon" id="profileIcon">
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
                                    <a href="/statuspesanan" class="profile-link">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i> Status pesanan
                                    </a>
                                    <a href="{{ route('logout') }}" class="profile-link"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>

                                    <!-- Tampilkan hanya jika role = admin -->
                                    @if(auth()->user()->role === 'admin')
                                        <a href="/admin" class="profile-link">
                                            <i class="fas fa-user-shield"></i> Admin Panel
                                        </a>
                                    @endif

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
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
                    </li>

                    <!-- Keranjang belanja -->
                    <li class="cart-icon" id="cartIcon" style="position: relative;">
                        @auth
                            <div style="cursor: pointer;">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count" id="cartCount">0</span>
                            </div>

                            <div class="cart-dropdown hidden" id="cartDropdown">
                                <div class="cart-empty" id="cartEmpty">
                                    <i class="fas fa-shopping-basket" style="font-size: 24px; margin-bottom: 10px;"></i>
                                    <p>Keranjang belanja Anda kosong</p>
                                </div>
                                <div class="cart-items hidden" id="cartItems"></div>
                                <div class="cart-footer hidden" id="cartFooter">
                                    <div class="cart-total" style="margin-bottom: 10px;">
                                        Total: <span id="cartTotal">Rp 0</span>
                                    </div>
                                    <button class="checkout-btn" id="checkoutBtn">Checkout</button>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" style="text-decoration: none; color: inherit;">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count">0</span>
                            </a>
                        @endauth
                    </li>
                </ul>
            </nav>
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
</body>

</html>