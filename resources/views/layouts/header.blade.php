<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catering Mama Zel - Warisan Rasa Asli Solo</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- AlpineJS -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-K7aO5wmtnKpu8KaH"></script>
  <script src="js/app.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  header {
    background-color: #fff;
    padding: 20px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
  }

  main {
    padding: 40px 20px;
    min-height: 60vh;
  }

  footer {
    background-color: #f8f8f8;
    padding: 40px 20px;
    margin-top: 40px;
    border-top: 1px solid #ddd;
  }
</style>

</head>

<body>

  <!-- ====== Bagian Navbar ====== -->
  <header>
    <div class="container">
      <a href="/">
        <img src="image/logo1.png" alt="Logo Catering Mama Zel" width="200">
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
            <i class="fa-solid fa-user" style="color: #BC5DFF;"></i>
            <div class="profile-dropdown" id="profileDropdown">
              <div class="profile-menu">
                @auth
                  <a href="/profile" class="profile-link"><i class="fa-solid fa-user" style="color: #BC5DFF;"></i> My Profile</a>
                  <a href="/settings" class="profile-link"><i class="fas fa-cog"></i> Settings</a>
                  <a href="{{ route('logout') }}" class="profile-link"
                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                @endauth

                @guest
                  <a href="{{ route('login') }}" class="profile-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                  <a href="{{ route('register') }}" class="profile-link"><i class="fas fa-user-plus"></i> Register</a>
                  <a href="https://dashboard.midtrans.com/" class="profile-link"><i class="fas fa-user-shield"></i> Admin Panel</a>
                @endguest
              </div>
            </div>
          </li>

          <!-- Cart -->
          <li class="cart-icon" id="cartIcon">
            <i class="fas fa-shopping-cart" style="color: #BC5DFF;"></i>
            <span class="cart-count" id="cartCount">0</span>
            <div class="cart-dropdown" id="cartDropdown">
              <div class="cart-empty" id="cartEmpty">
                <i class="fas fa-shopping-basket" style="font-size: 24px; margin-bottom: 10px;"></i>
                <p>Keranjang belanja Anda kosong</p>
              </div>
              <div class="cart-items" id="cartItems">
                <!-- Cart items will be added here dynamically -->
              </div>
              <div class="cart-footer" id="cartFooter" style="display: none;">
                <div class="cart-total" style="margin-bottom: 10px;">
                  Total: <span id="cartTotal">Rp 0</span>
                </div>
                <button class="checkout-btn">Checkout</button>
              </div>
            </div>
          </li>
        </ul>
      </nav>
      <div class="menu-toggle">
        <i class="fas fa-bars"></i>
      </div>
    </div>
  </header>

  <!-- ====== Bagian Konten Utama ====== -->
  <main>
    @yield('content')
  </main>

  <!-- ====== Bagian Footer ====== -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo">
          <h2>Catering Mama Zel</h2>
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
        <div class="footer-newsletter">
          <h3>Berlangganan Newsletter</h3>
          <p>Dapatkan informasi terbaru dan promosi menarik</p>
          <form action="#" method="post">
            <input type="email" placeholder="Alamat Email Anda" required>
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
          </form>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Catering Mama Zel. Hak Cipta Dilindungi.</p>
      </div>
    </div>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
