<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - Teh Tarhadi</title>
    <!-- Gunakan satu script Midtrans dengan client key yang benar -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-K7aO5wmtnKpu8KaH"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS styles tetap sama -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            color: #2d5016;
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header .tea-icon {
            font-size: 2rem;
            color: #6b8e23;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .checkout-section {
            background: white;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .section-title {
            color: #2d5016;
            font-size: 1.8rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #6b8e23;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: #6b8e23;
            background: white;
            box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
        }

        .form-control:valid {
            border-color: #28a745;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .order-summary {
            background: linear-gradient(135deg, #6b8e23 0%, #8fbc8f 100%);
            color: white;
            padding: 25px;
            margin-bottom: 20px;
        }

        .order-summary h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .checkout-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
        }

        .checkout-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .checkout-item-details {
            flex: 1;
        }

        .checkout-item-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .checkout-item-price {
            opacity: 0.9;
            font-size: 1rem;
        }

        .checkout-item-total {
            font-size: 1.3rem;
            font-weight: 700;
            text-align: right;
        }

        .total-section {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .total-row.final {
            font-size: 1.4rem;
            font-weight: 700;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            margin-top: 15px;
        }

        .payment-methods {
            margin: 25px 0;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .payment-method input[type="radio"] {
            display: none;
        }

        .payment-method .radio-custom {
            width: 20px;
            height: 20px;
            border: 2px solid white;
            position: relative;
        }

        .payment-method input[type="radio"]:checked+.radio-custom::after {
            content: '';
            width: 10px;
            height: 10px;
            background: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .pay-button {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            border: none;
            font-size: 1.3rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        .pay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.6);
        }

        .pay-button:active {
            transform: translateY(0);
        }

        .pay-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .security-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #6b8e23;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: #2d5016;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ef5350;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #6b8e23;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .checkout-section {
                padding: 20px;
            }
        }
    </style>

</head>

<body data-user-id="{{ auth()->id() ?? 'guest' }}">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-leaf tea-icon"></i>
                Teh Tarhadi
            </h1>
            <p>Checkout Pesanan - Teh Berkualitas Terbaik</p>
        </div>

        <!-- Back Button -->
        <a href="/produk" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Produk
        </a>

        <!-- Main Content -->
        <div class="checkout-grid">
            <!-- Customer Form -->
            <div class="checkout-section">
                <h2 class="section-title">
                    <i class="fas fa-user"></i>
                    Informasi Pelanggan
                </h2>

                <!-- PERBAIKAN: Tambahkan div untuk menampilkan error -->
                <div id="error-container"></div>

                <form id="checkout-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">Nama Depan *</label>
                            <input type="text" id="first-name" name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="last-name">Nama Belakang *</label>
                            <input type="text" id="last-name" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Nomor Telepon *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat Lengkap *</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required
                            placeholder="Masukkan alamat lengkap termasuk nama jalan, nomor rumah, RT/RW"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Kota *</label>
                            <input type="text" id="city" name="city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="postal-code">Kode Pos *</label>
                            <input type="text" id="postal-code" name="postal_code" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="province">Provinsi *</label>
                        <select id="province" name="province" class="form-control" required>
                            <option value="">Pilih Provinsi</option>
                            <option value="DKI Jakarta">DKI Jakarta</option>
                            <option value="Jawa Barat">Jawa Barat</option>
                            <option value="Jawa Tengah">Jawa Tengah</option>
                            <option value="Jawa Timur">Jawa Timur</option>
                            <option value="Banten">Banten</option>
                            <option value="Yogyakarta">D.I. Yogyakarta</option>
                            <option value="Bali">Bali</option>
                            <option value="Sumatera Utara">Sumatera Utara</option>
                            <option value="Sumatera Barat">Sumatera Barat</option>
                            <option value="Sumatera Selatan">Sumatera Selatan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan Tambahan</label>
                        <textarea id="notes" name="notes" class="form-control" rows="2"
                            placeholder="Catatan khusus untuk pesanan Anda (opsional)"></textarea>
                    </div>
                </form>
            </div>


            <!-- Order Summary -->
            <div class="checkout-section">
                <div class="order-summary">
                    <h3><i class="fas fa-shopping-bag"></i> Ringkasan Pesanan</h3>

                    <div id="checkout-items">
                        <!-- Items akan dimuat via JavaScript -->
                    </div>

                    <div class="total-section">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">Rp 0</span>
                        </div>
                        <div class="total-row">
                            <span>Ongkir:</span>
                            <span id="shipping">Rp 10,000</span>
                        </div>
                        <div class="total-row final">
                            <span>Total:</span>
                            <span id="total-amount">Rp 10,000</span>
                        </div>
                    </div>

                    <div class="payment-methods">
                        <h4 style="margin-bottom: 15px;"><i class="fas fa-credit-card"></i> Metode Pembayaran</h4>
                        <label class="payment-method">
                            <input type="radio" name="payment" value="all" checked>
                            <div class="radio-custom"></div>
                            <span>Metode Pembayaran (Bank Transfer BCA, Qris Gopay), ShopePay, Dana</span>
                        </label>
                    </div>

                    <button type="button" id="pay-button" class="pay-button">
                        <i class="fas fa-lock"></i> Bayar Sekarang
                    </button>

                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <span>Pembayaran aman dengan Midtrans</span>
                    </div>
                </div>

                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Memproses pembayaran...</p>
                </div>
            </div>
        </div>

        <!-- Empty Cart Message -->
        <div class="empty-cart" id="empty-cart" style="display: none;">
            <i class="fas fa-shopping-cart"></i>
            <h3>Keranjang Kosong</h3>
            <p>Belum ada produk dalam keranjang belanja Anda.</p>
            <a href="/produk" class="back-button" style="margin-top: 20px;">
                <i class="fas fa-arrow-left"></i>
                Mulai Belanja
            </a>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan error
        function showError(message) {
            const errorContainer = document.getElementById('error-container');
            errorContainer.innerHTML = `<div class="error-message">${message}</div>`;
            setTimeout(() => {
                errorContainer.innerHTML = '';
            }, 5000);
        }

        // Fungsi untuk validasi form
        function validateForm() {
            const requiredFields = [
                'first-name', 'last-name', 'email', 'phone',
                'address', 'city', 'postal-code', 'province'
            ];

            let isValid = true;
            for (const fieldId of requiredFields) {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.style.borderColor = '#ff6b6b';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e0e0e0';
                }
            }

            return isValid;
        }

        // Load cart data
        function loadCheckoutData() {
            const userId = document.body.dataset?.userId || 'guest';
            const cartKey = `cart_${userId}`;
            const savedCart = localStorage.getItem(cartKey);
            return savedCart ? JSON.parse(savedCart) : [];
        }

        // Display checkout items
        function displayCheckoutItems() {
            const cart = loadCheckoutData();
            const itemsContainer = document.getElementById('checkout-items');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total-amount');

            // Check if cart is empty
            if (!cart || cart.length === 0) {
                document.querySelector('.checkout-grid').style.display = 'none';
                document.getElementById('empty-cart').style.display = 'block';
                return;
            }

            let subtotal = 0;
            itemsContainer.innerHTML = '';

            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'checkout-item';
                itemDiv.innerHTML = `
                    <img src="${item.img}" alt="${item.name}" class="checkout-item-img">
                    <div class="checkout-item-details">
                        <div class="checkout-item-name">${item.name}</div>
                        <div class="checkout-item-price">Rp ${item.price.toLocaleString()} × ${item.quantity}</div>
                    </div>
                    <div class="checkout-item-total">Rp ${itemTotal.toLocaleString()}</div>
                `;
                itemsContainer.appendChild(itemDiv);
            });

            const shipping = 10000;
            const total = subtotal + shipping;

            subtotalElement.textContent = `Rp ${subtotal.toLocaleString()}`;
            totalElement.textContent = `Rp ${total.toLocaleString()}`;
        }

        // Perbaikan pada bagian Handle payment - bagian formatting items
        document.getElementById('pay-button').addEventListener('click', async function () {
            console.log('🔄 Payment button clicked');

            // Validasi form sebelum submit
            if (!validateForm()) {
                showError('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }

            const userId = document.body.dataset.userId || 'guest';
            const cartKey = `cart_${userId}`;
            const cart = JSON.parse(localStorage.getItem(cartKey) || '[]');

            if (cart.length === 0) {
                showError('Keranjang belanja kosong!');
                return;
            }

            // Disable button dan show loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            document.getElementById('loading').style.display = 'block';

            try {
                // Ambil data form pelanggan
                const customer = {
                    first_name: document.getElementById('first-name').value.trim(),
                    last_name: document.getElementById('last-name').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    phone: document.getElementById('phone').value.trim(),
                    address: document.getElementById('address').value.trim(),
                    city: document.getElementById('city').value.trim(),
                    postal_code: document.getElementById('postal-code').value.trim(),
                    province: document.getElementById('province').value,
                    notes: document.getElementById('notes').value.trim(),
                };

                // Hitung dengan benar
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const shipping = 10000;
                const total = subtotal + shipping;

                // PERBAIKAN: Format items sesuai standar Midtrans
                const formattedItems = cart.map((item, index) => {
                    // Pastikan nama produk tidak kosong dan tidak terlalu panjang
                    const itemName = (item.name || 'Produk Teh').substring(0, 50);
                    const itemPrice = parseInt(item.price) || 0;
                    const itemQuantity = parseInt(item.quantity) || 1;

                    return {
                        id: item.id || `TEH_${index + 1}_${Date.now()}`, // ID yang unik dan deskriptif
                        price: itemPrice, // Harga per unit
                        quantity: itemQuantity, // Kuantitas
                        name: itemName, // Nama produk (maksimal 50 karakter)
                        brand: "Teh Tarhadi", // Brand produk
                        category: "Minuman", // Kategori produk
                        merchant_name: "Teh Tarhadi" // Nama merchant
                    };
                });

                // PERBAIKAN: Tambahkan item shipping sebagai item terpisah
                const itemDetails = [
                    ...formattedItems,
                    {
                        id: "SHIPPING",
                        price: shipping,
                        quantity: 1,
                        name: "Biaya Pengiriman",
                        category: "Shipping"
                    }
                ];

                console.log('📦 Formatted items:', formattedItems);
                console.log('📦 Item details with shipping:', itemDetails);

                const dataToSend = {
                    customer: customer,
                    items: formattedItems, // Items untuk backend processing
                    item_details: itemDetails, // Item details untuk Midtrans
                    subtotal: parseInt(subtotal),
                    shipping_cost: parseInt(shipping),
                    total: parseInt(total),
                    // TAMBAHAN: Informasi transaksi
                    transaction_details: {
                        order_id: `TEH_ORDER_${Date.now()}`,
                        gross_amount: parseInt(total)
                    }
                };

                console.log('📤 Data yang akan dikirim:', dataToSend);

                // Kirim request ke server
                const response = await fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(dataToSend)
                });

                console.log('📥 Response status:', response.status);

                // Parse response
                let result;
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    const textResponse = await response.text();
                    console.error('❌ Non-JSON response:', textResponse);
                    throw new Error('Server mengembalikan response yang tidak valid');
                }

                console.log('📥 Response dari server:', result);

                // Handle error response
                if (!response.ok) {
                    if (result.validation_errors) {
                        let errorMessage = 'Data tidak valid:\n';
                        Object.entries(result.validation_errors).forEach(([field, errors]) => {
                            errorMessage += `- ${field}: ${errors.join(', ')}\n`;
                        });
                        throw new Error(errorMessage);
                    } else {
                        throw new Error(result.error || `HTTP ${response.status}: ${response.statusText}`);
                    }
                }

                if (!result.success) {
                    throw new Error(result.error || 'Terjadi kesalahan pada server');
                }

                if (!result.snap_token) {
                    throw new Error('Tidak dapat memperoleh token pembayaran dari server');
                }

                console.log('✅ Snap token diterima, membuka Midtrans...');

                if (typeof window.snap === 'undefined') {
                    throw new Error('Midtrans Snap tidak tersedia. Mohon refresh halaman.');
                }

                window.snap.pay(result.snap_token, {
                    onSuccess: function (result) {
                        console.log('✅ Payment success:', result);
                        alert("Pembayaran berhasil! Terima kasih atas pesanan Anda.");
                        localStorage.removeItem(cartKey);
                        window.location.href = "/statuspesanan";
                    },
                    onPending: function (result) {
                        console.log('⏳ Payment pending:', result);
                        alert("Transaksi sedang diproses. Silakan selesaikan pembayaran Anda.");
                        localStorage.removeItem(cartKey);
                        window.location.href = "/statuspesanan";
                    },
                    onError: function (result) {
                        console.error('❌ Payment error:', result);
                        alert("Pembayaran gagal. Silakan coba lagi.");
                    },
                    onClose: function () {
                        console.log('❌ Payment popup closed');
                        alert("Pembayaran dibatalkan. Pesanan Anda masih tersimpan.");
                    }
                });

            } catch (error) {
                console.error('❌ Checkout Error:', error);
                showError(error.message || "Gagal memproses pembayaran. Silakan coba lagi.");
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-lock"></i> Bayar Sekarang';
                document.getElementById('loading').style.display = 'none';
            }
        });

        // TAMBAHAN: Fungsi untuk debug cart data
        function debugCartData() {
            const userId = document.body.dataset.userId || 'guest';
            const cartKey = `cart_${userId}`;
            const cart = JSON.parse(localStorage.getItem(cartKey) || '[]');

            console.log('🛒 Current cart data:', cart);

            if (cart.length > 0) {
                console.log('📋 Cart structure analysis:');
                cart.forEach((item, index) => {
                    console.log(`Item ${index + 1}:`, {
                        id: item.id,
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity,
                        img: item.img
                    });
                });
            }

            return cart;
        }

        // Panggil fungsi debug saat halaman load
        document.addEventListener('DOMContentLoaded', function () {
            displayCheckoutItems(); // Tampilkan items checkout
            debugCartData(); // Debug cart data

            // Check if Midtrans Snap is loaded
            if (typeof window.snap === 'undefined') {
                console.error('Midtrans Snap tidak dimuat. Periksa client key dan koneksi internet.');
                showError('Error: Midtrans Snap tidak dapat dimuat. Mohon refresh halaman.');
            }
        });
    </script>
</body>

</html>