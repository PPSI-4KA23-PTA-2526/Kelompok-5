// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', function () {
  // Mobile Menu Toggle
  const menuToggle = document.querySelector('.menu-toggle');
  const navMenu = document.querySelector('nav ul');

  menuToggle.addEventListener('click', function () {
    navMenu.classList.toggle('show');
  });

  // Smooth Scrolling for Navigation Links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();

      // Close mobile menu if open
      if (navMenu.classList.contains('show')) {
        navMenu.classList.remove('show');
      }

      const targetId = this.getAttribute('href');
      const targetElement = document.querySelector(targetId);

      if (targetElement) {
        // Adjust for fixed header
        const headerHeight = document.querySelector('header').offsetHeight;
        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;

        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

  // Sticky Header
  const header = document.querySelector('header');
  const heroSection = document.querySelector('#hero');

  function toggleStickyHeader() {
    if (window.scrollY > 0) {
      header.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
      header.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
    } else {
      header.style.backgroundColor = 'var(--white-color)';
      header.style.boxShadow = 'none';
    }
  }

  window.addEventListener('scroll', toggleStickyHeader);

  // Form Validation
  const contactForm = document.querySelector('#kontak form');

  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // Get form values
      const nama = document.getElementById('nama').value;
      const email = document.getElementById('email').value;
      const pesan = document.getElementById('pesan').value;

      // Simple validation
      if (nama.trim() === '' || email.trim() === '' || pesan.trim() === '') {
        alert('Mohon isi semua field yang wajib diisi!');
        return;
      }

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert('Mohon masukkan alamat email yang valid!');
        return;
      }

      // If validation passes, you would usually submit the form
      // For demo purposes, we'll just show a success message
      alert('Terima kasih! Pesan Anda telah berhasil dikirim.');
      contactForm.reset();
    });
  }

  // Newsletter Form Validation
  const newsletterForm = document.querySelector('.footer-newsletter form');

  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const email = this.querySelector('input[type="email"]').value;

      // Simple validation
      if (email.trim() === '') {
        alert('Mohon masukkan alamat email Anda!');
        return;
      }

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert('Mohon masukkan alamat email yang valid!');
        return;
      }

      // If validation passes
      alert('Terima kasih telah berlangganan newsletter kami!');
      newsletterForm.reset();
    });
  }

  // Animation on Scroll
  function revealOnScroll() {
    const elements = document.querySelectorAll('.fitur-item, .produk-card, .testimonial-item, .proses-item');

    elements.forEach(element => {
      const elementTop = element.getBoundingClientRect().top;
      const windowHeight = window.innerHeight;

      if (elementTop < windowHeight - 50) {
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
      }
    });
  }

  // Set initial state for animation
  const animateElements = document.querySelectorAll('.fitur-item, .produk-card, .testimonial-item, .proses-item');
  animateElements.forEach(element => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(20px)';
    element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  });

  // Run animation on page load and scroll
  window.addEventListener('load', revealOnScroll);
  window.addEventListener('scroll', revealOnScroll);
  window.addEventListener('scroll', function () {
    const header = document.querySelector('header');
    header.classList.toggle('scrolled', window.scrollY > 50);
  });
});

document.addEventListener('DOMContentLoaded', function () {
  // Shopping cart functionality
  const cartIcon = document.getElementById('cartIcon');
  const cartDropdown = document.getElementById('cartDropdown');
  const cartCount = document.getElementById('cartCount');
  const cartEmpty = document.getElementById('cartEmpty');
  const cartItems = document.getElementById('cartItems');
  const cartFooter = document.getElementById('cartFooter');
  const cartTotal = document.getElementById('cartTotal');

  // Profile functionality
  const profileIcon = document.getElementById('profileIcon');
  const profileDropdown = document.getElementById('profileDropdown');

  // Cart data
  let cart = [];

  // Customer data
  let customerData = {
    name: '',
    email: '',
    phone: ''
  };

  // Toggle cart dropdown
  cartIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    if (cartDropdown.classList.contains('show')) {
      cartDropdown.classList.remove('show');
    } else {
      // Update cart view before showing to ensure latest data is displayed
      updateCart();
      cartDropdown.classList.add('show');
    }

    // Close profile dropdown if open
    if (profileDropdown.classList.contains('show')) {
      profileDropdown.classList.remove('show');
    }
  });

  // Toggle profile dropdown
  profileIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    profileDropdown.classList.toggle('show');

    // Close cart dropdown if open
    if (cartDropdown.classList.contains('show')) {
      cartDropdown.classList.remove('show');
    }
  });

  // Close dropdowns when clicking outside
  document.addEventListener('click', function (e) {
    // For cart dropdown: don't close when clicking anywhere inside it
    if (cartDropdown.contains(e.target) || cartIcon.contains(e.target)) {
      return; // Don't close cart dropdown if clicking inside it or on cart icon
    }

    // If we got here, user clicked outside cart - close it
    if (cartDropdown.classList.contains('show')) {
      cartDropdown.classList.remove('show');
    }

    // Close profile dropdown when clicking outside
    if (!profileIcon.contains(e.target) && !profileDropdown.contains(e.target) && profileDropdown.classList.contains('show')) {
      profileDropdown.classList.remove('show');
    }
  });

  // keranjang belanja
  // Add to cart functionality
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function (e) {
      const isLoggedIn = document.body.dataset.loggedIn === 'true';

      if (!isLoggedIn) {
        window.location.href = '/login';
        return;
      }

      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      const price = parseFloat(this.getAttribute('data-price'));
      const img = this.getAttribute('data-img');

      addToCart(id, name, price, img);

      cartIcon.classList.add('flash');
      setTimeout(() => cartIcon.classList.remove('flash'), 300);
    });
  });


  function addToCart(id, name, price, img, quantity = 1) {
    // Check if product already in cart
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      cart.push({
        id: id,
        name: name,
        price: price,
        img: img,
        quantity: quantity
      });
    }

    // Save cart to localStorage
    saveCart();
    updateCart();
  }

  function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
    updateCart();
  }

  function updateQuantity(id, newQuantity) {
    const item = cart.find(item => item.id === id);
    if (item) {
      // Ensure quantity is at least 1
      item.quantity = Math.max(1, newQuantity);
      saveCart();
      updateCart();
    }
  }

  // Ambil user ID dari tag <body>
  const userId = document.body.dataset.userId || 'guest';
  const cartKey = `cart_${userId}`;
  const customerKey = `customerData_${userId}`;

  function saveCart() {
    localStorage.setItem(cartKey, JSON.stringify(cart));
  }

  function saveCustomerData() {
    localStorage.setItem(customerKey, JSON.stringify(customerData));
  }

  function loadCart() {
    const savedCart = localStorage.getItem(cartKey);
    if (savedCart) {
      cart = JSON.parse(savedCart);
      updateCart();
    }

    const savedCustomer = localStorage.getItem(customerKey);
    if (savedCustomer) {
      customerData = JSON.parse(savedCustomer);
      updateCustomerForm();
    }
  }


  function updateCustomerForm() {
    if (document.getElementById('customer-name')) {
      document.getElementById('customer-name').value = customerData.name;
      document.getElementById('customer-email').value = customerData.email;
      document.getElementById('customer-phone').value = customerData.phone;
    }
  }

  function updateCart() {
    // Update cart count
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    cartCount.textContent = totalItems;

    // Show/hide empty cart message
    if (cart.length === 0) {
      cartEmpty.style.display = 'flex';
      cartItems.innerHTML = '';
      cartFooter.style.display = 'none';

      // Remove customer details section if present
      const existingCustomerSection = document.querySelector('.customer-details');
      if (existingCustomerSection) {
        existingCustomerSection.remove();
      }
    } else {
      cartEmpty.style.display = 'none';

      // Update cart items
      cartItems.innerHTML = '';

      cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
          <img src="${item.img}" alt="${item.name}" class="cart-item-img">
          <div class="cart-item-details">
            <div class="cart-item-title">${item.name}</div>
            <div class="cart-item-price">Rp ${item.price.toLocaleString()}</div>
            <div class="cart-item-quantity">
              <button class="quantity-btn minus" data-id="${item.id}">
                <i class="fas fa-minus"></i>
              </button>
              <span class="quantity-value">${item.quantity}</span>
              <button class="quantity-btn plus" data-id="${item.id}">
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>
          <div class="cart-item-subtotal">= Rp ${(item.price * item.quantity).toLocaleString()}</div>
          <button class="cart-item-remove" data-id="${item.id}">
            <i class="fas fa-times"></i>
          </button>
        `;
        cartItems.appendChild(cartItem);

        // Add quantity change functionality
        cartItem.querySelector('.minus').addEventListener('click', function (e) {
          e.stopPropagation(); // Stop event propagation
          const id = this.getAttribute('data-id');
          const item = cart.find(item => item.id === id);
          if (item && item.quantity > 1) {
            updateQuantity(id, item.quantity - 1);
          }
        });

        cartItem.querySelector('.plus').addEventListener('click', function (e) {
          e.stopPropagation(); // Stop event propagation
          const id = this.getAttribute('data-id');
          const item = cart.find(item => item.id === id);
          if (item) {
            updateQuantity(id, item.quantity + 1);
          }
        });

        // Add remove functionality
        cartItem.querySelector('.cart-item-remove').addEventListener('click', function (e) {
          e.stopPropagation(); // Stop event propagation
          removeFromCart(this.getAttribute('data-id'));
        });
      });

      // Update total
      const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      cartTotal.textContent = `Rp ${total.toLocaleString()}`;

      // Show footer
      cartFooter.style.display = 'block';

    }
  }

  // Set up checkout button with Midtrans integration
  const checkoutBtn = document.querySelector('.checkout-btn');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', function () {
      if (cart.length === 0) {
        alert('Keranjang belanja kosong!');
        return;
      }

      // Tidak perlu set ulang localStorage, karena sudah disimpan saat tambahkan ke keranjang
      window.location.href = '/checkout';
    });
  }


  // Load cart data from localStorage when page loads
  loadCart();

  // Apply CSS for the customer details section
  const style = document.createElement('style');
  style.textContent = `
    .customer-details {
      padding: 15px;
      border-top: 1px solid #eee;
      margin-top: 10px;
    }
    
    .customer-details-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #333;
    }
    
    .customer-form .form-group {
      margin-bottom: 10px;
    }
    
    .customer-form label {
      display: block;
      font-size: 12px;
      color: #666;
      margin-bottom: 3px;
    }
    
    .customer-form .form-control {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    
    .checkout-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    
    .checkout-btn:hover {
      background-color: #45a049;
    }
    
    .checkout-btn:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
    }
    
    /* Style for empty input validation */
    .form-control.invalid {
      border-color: #ff6b6b;
      background-color: #fff0f0;
    }
    
    /* Cart animation */
    .flash {
      animation: flash-animation 0.3s;
    }
    
    @keyframes flash-animation {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }
  `;
  document.head.appendChild(style);
});
//end keranjang belanja

document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', function (e) {
    if (!isLoggedIn) {
      alert("Silakan login terlebih dahulu untuk menambahkan ke keranjang.");
      window.location.href = "/login";
      return;
    }

    // Lanjutkan logika add to cart
    const productId = this.dataset.id;
    const productName = this.dataset.name;
    const productPrice = this.dataset.price;
    const productImg = this.dataset.img;

    // Misalnya simpan ke localStorage atau kirim ke backend
    console.log("Tambah ke keranjang:", productName);
  });
});

function checkLogin() {
  if (!isLoggedIn) {
    alert("Silakan login untuk melanjutkan pembelian.");
    window.location.href = "/login";
    return false;
  }
  return true;
}


//checkout

document.addEventListener('DOMContentLoaded', function () {
  // Load cart data from localStorage
  function loadCart() {
    const userId = document.body.dataset.userId || 'guest';
    const cartKey = `cart_${userId}`;
    const savedCart = localStorage.getItem(cartKey);
    return savedCart ? JSON.parse(savedCart) : [];
  }


  // Display items in checkout
  function displayCheckoutItems() {
    const cart = loadCart();
    const checkoutItemsContainer = document.getElementById('checkout-items');
    const checkoutTotalAmount = document.getElementById('checkout-total-amount');

    // Clear existing content
    checkoutItemsContainer.innerHTML = '';

    if (cart.length === 0) {
      // Redirect to products page if cart is empty
      window.location.href = '/produk';
      return;
    }

    let total = 0;

    // Add each item to the checkout summary
    cart.forEach(item => {
      const itemTotal = item.price * item.quantity;
      total += itemTotal;

      const itemElement = document.createElement('div');
      itemElement.className = 'checkout-item';
      itemElement.innerHTML = `
                        <div class="checkout-item-details">
                            <img src="${item.img}" alt="${item.name}" class="checkout-item-img">
                            <div>
                                <div class="checkout-item-name">${item.name}</div>
                                <div class="checkout-item-price">Rp ${item.price.toLocaleString()} × ${item.quantity}</div>
                            </div>
                        </div>
                        <div class="checkout-item-total">Rp ${itemTotal.toLocaleString()}</div>
                    `;

      checkoutItemsContainer.appendChild(itemElement);
    });

    // Update total amount
    checkoutTotalAmount.textContent = `Rp ${total.toLocaleString()}`;
  }

  // Handle form submission
  const checkoutForm = document.getElementById('checkout-form');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // Get form values
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const phone = document.getElementById('phone').value;
      const address = document.getElementById('address').value;
      const notes = document.getElementById('notes').value;

      // Create order object
      const order = {
        customer: {
          name,
          email,
          phone,
          address,
          notes
        },
        items: loadCart(),
        total: loadCart().reduce((sum, item) => sum + (item.price * item.quantity), 0),
        orderDate: new Date().toISOString()
      };

      // Here you would typically send this data to your server
      console.log('Order submitted:', order);

      // For demonstration, redirect to a thank you page
      alert('Terima kasih! Pesanan Anda telah diterima.');

      // Clear cart and redirect
      localStorage.removeItem('cart');
      window.location.href = '/';
    });
  }

  // Initialize checkout page
  displayCheckoutItems();
});

const cartIcon = document.getElementById("cartIcon");
const cartDropdown = document.getElementById("cartDropdown");

cartIcon.addEventListener("click", function () {
  cartDropdown.classList.toggle("hidden");
});

    // Ambil tombol-tombol beli sekarang
    document.querySelectorAll('.btn-produk').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Cegah redirect langsung

            const card = this.closest('.produk-card');
            const addToCartBtn = card.querySelector('.add-to-cart');

            const id = addToCartBtn.dataset.id;
            const name = addToCartBtn.dataset.name;
            const price = addToCartBtn.dataset.price;
            const img = addToCartBtn.dataset.img;

            // Tambahkan ke localStorage/cart kamu (jika pakai localStorage)
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const exists = cart.find(item => item.id === id);
            if (!exists) {
                cart.push({ id, name, price, img, quantity: 1 });
                localStorage.setItem('cart', JSON.stringify(cart));
            }

            // Redirect ke checkout
            window.location.href = "/checkout";
        });
    });

