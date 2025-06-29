{{-- File: resources/views/produk.blade.php --}}
@extends('layouts.navfott')

@section('title', 'Produk - Teh Tarhadi')

@section('content')

    <!-- ====== Bagian Produk Kemasan ====== -->
    <section id="produk" class="section-produk">
        <div class="container">
            <div class="section-title">
                <h2>Produk Kami</h2>
                <p>Temukan berbagai varian teh berkualitas dari Teh Tarhadi</p>
            </div>
            <div class="produk-grid">
                @forelse($products->where('category', 'kemasan') as $product)
                <div class="produk-card" data-product-id="{{ $product->id }}">
                    <div class="produk-img">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/default.png') }}"
                             alt="{{ $product->name }}"
                             onerror="this.src='{{ asset('image/default.png') }}'">
                        
                        {{-- Stock Badge --}}
                        @if($product->stock <= 0)
                            <div class="stock-badge out-of-stock">
                                <span>Habis</span>
                            </div>
                        @elseif($product->stock <= 5)
                            <div class="stock-badge low-stock">
                                <span>Stok Terbatas</span>
                            </div>
                        @endif
                    </div>
                    <div class="produk-info">
                        <h3>{{ $product->name }}</h3>
                        <p>{{ $product->description }}</p>
                        <div class="produk-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        
                        {{-- Stock Info --}}
                        <div class="stock-info">
                            <i class="fas fa-box"></i>
                            <span class="stock-count" data-product-id="{{ $product->id }}">
                                {{ $product->stock }} tersisa
                            </span>
                        </div>
                        
                        <div class="produk-btn-wrapper">
                            @if($product->stock > 0)
                                <button class="btn add-to-cart"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}"
                                        data-img="{{ $product->image ? asset('storage/' . $product->image) : asset('image/default.png') }}">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                                <a href="/checkout?product_id={{ $product->id }}" class="btn-produk">Beli Sekarang</a>
                            @else
                                <button class="btn add-to-cart disabled" disabled>
                                    <i class="fas fa-ban"></i>
                                </button>
                                <button class="btn-produk disabled" disabled>Stok Habis</button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
        
                @endforelse
                
                {{-- Jika tidak ada kategori, tampilkan semua produk --}}
                @if($products->where('category', 'kemasan')->isEmpty() && $products->count() > 0)
                    @foreach($products as $product)
                    <div class="produk-card" data-product-id="{{ $product->id }}">
                        <div class="produk-img">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/default.png') }}"
                                 alt="{{ $product->name }}"
                                 onerror="this.src='{{ asset('image/default.png') }}'">
                            
                            {{-- Stock Badge --}}
                            @if($product->stock <= 0)
                                <div class="stock-badge out-of-stock">
                                    <span>Habis</span>
                                </div>
                            @elseif($product->stock <= 5)
                                <div class="stock-badge low-stock">
                                    <span>Stok Terbatas</span>
                                </div>
                            @endif
                        </div>
                        <div class="produk-info">
                            <h3>{{ $product->name }}</h3>
                            <p>{{ $product->description }}</p>
                            <div class="produk-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            
                            {{-- Stock Info --}}
                            <div class="stock-info">
                                <i class="fas fa-box"></i>
                                <span class="stock-count" data-product-id="{{ $product->id }}">
                                    {{ $product->stock }} tersisa
                                </span>
                            </div>
                            
                            <div class="produk-btn-wrapper">
                                @if($product->stock > 0)
                                    <button class="btn add-to-cart"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-price="{{ $product->price }}"
                                            data-stock="{{ $product->stock }}"
                                            data-img="{{ $product->image ? asset('storage/' . $product->image) : asset('image/default.png') }}">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                    <a href="/checkout?product_id={{ $product->id }}" class="btn-produk">Beli Sekarang</a>
                                @else
                                    <button class="btn add-to-cart disabled" disabled>
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <button class="btn-produk disabled" disabled>Stok Habis</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- ====== Produk Siap Minum ====== -->
    @if($products->where('category', 'siap_minum')->count() > 0)
    <section id="produk-siap-minum" class="section-produk">
        <div class="container">
            <div class="section-title" style="margin-top: -250px;">
                <h2>Produk Teh Siap Minum</h2>
                <p>Kami juga menghadirkan produk teh siap minum yang praktis dinikmati dan menyegarkan untuk menemani harimu.</p>
            </div>
            <div class="produk-grid-jadi">
                @foreach($products->where('category', 'siap_minum') as $product)
                <div class="produk-card" data-product-id="{{ $product->id }}">
                    <div class="produk-img">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/default.png') }}"
                             alt="{{ $product->name }}"
                             onerror="this.src='{{ asset('image/default.png') }}'">
                        
                        {{-- Stock Badge --}}
                        @if($product->stock <= 0)
                            <div class="stock-badge out-of-stock">
                                <span>Habis</span>
                            </div>
                        @elseif($product->stock <= 5)
                            <div class="stock-badge low-stock">
                                <span>Stok Terbatas</span>
                            </div>
                        @endif
                    </div>
                    <div class="produk-info">
                        <h3>{{ $product->name }}</h3>
                        <p>{{ $product->description }}</p>
                        <div class="produk-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        
                        {{-- Stock Info --}}
                        <div class="stock-info">
                            <i class="fas fa-box"></i>
                            <span class="stock-count" data-product-id="{{ $product->id }}">
                                {{ $product->stock }} tersisa
                            </span>
                        </div>
                        
                        <div class="produk-btn-wrapper">
                            @if($product->stock > 0)
                                <button class="btn add-to-cart"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}"
                                        data-img="{{ $product->image ? asset('storage/' . $product->image) : asset('image/default.png') }}">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                                <a href="/checkout?product_id={{ $product->id }}" class="btn-produk">Beli Sekarang</a>
                            @else
                                <button class="btn add-to-cart disabled" disabled>
                                    <i class="fas fa-ban"></i>
                                </button>
                                <button class="btn-produk disabled" disabled>Stok Habis</button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection

{{-- Section untuk CSS khusus stock --}}
@section('styles')
<style>
.stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    z-index: 10;
}

.stock-badge.out-of-stock {
    background-color: #dc3545;
}

.stock-badge.low-stock {
    background-color: #ffc107;
    color: #000;
}

.stock-info {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 10px 0;
    font-size: 14px;
    color: #666;
}

.stock-info i {
    color: #28a745;
}

.stock-count {
    font-weight: 500;
}

.produk-card .disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #6c757d !important;
}

.produk-card .disabled:hover {
    background-color: #6c757d !important;
}

.produk-img {
    position: relative;
}

/* Animasi untuk update stock */
.stock-count.updating {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>
@endsection

{{-- Section untuk JavaScript realtime stock --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk update stock secara realtime
    function updateStock() {
        fetch('/api/products/stock')
            .then(response => response.json())
            .then(data => {
                data.forEach(product => {
                    const stockElement = document.querySelector(`.stock-count[data-product-id="${product.id}"]`);
                    const productCard = document.querySelector(`.produk-card[data-product-id="${product.id}"]`);
                    
                    if (stockElement) {
                        // Update jumlah stock
                        stockElement.textContent = `${product.stock} tersisa`;
                        stockElement.classList.add('updating');
                        
                        // Remove animation class after animation completes
                        setTimeout(() => {
                            stockElement.classList.remove('updating');
                        }, 500);
                        
                        // Update badge dan button status
                        updateProductCard(productCard, product);
                    }
                });
            })
            .catch(error => console.error('Error updating stock:', error));
    }
    
    function updateProductCard(productCard, product) {
        if (!productCard) return;
        
        const addToCartBtn = productCard.querySelector('.add-to-cart');
        const buyNowBtn = productCard.querySelector('.btn-produk');
        const stockBadge = productCard.querySelector('.stock-badge');
        const produkImg = productCard.querySelector('.produk-img');
        
        // Remove existing badges
        if (stockBadge) {
            stockBadge.remove();
        }
        
        // Add new badge based on stock
        if (product.stock <= 0) {
            const badge = document.createElement('div');
            badge.className = 'stock-badge out-of-stock';
            badge.innerHTML = '<span>Habis</span>';
            produkImg.appendChild(badge);
            
            // Disable buttons
            addToCartBtn.classList.add('disabled');
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fas fa-ban"></i>';
            
            buyNowBtn.classList.add('disabled');
            buyNowBtn.textContent = 'Stok Habis';
            buyNowBtn.style.pointerEvents = 'none';
            
        } else if (product.stock <= 5) {
            const badge = document.createElement('div');
            badge.className = 'stock-badge low-stock';
            badge.innerHTML = '<span>Stok Terbatas</span>';
            produkImg.appendChild(badge);
            
            // Enable buttons
            addToCartBtn.classList.remove('disabled');
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-cart-plus"></i>';
            
            buyNowBtn.classList.remove('disabled');
            buyNowBtn.textContent = 'Beli Sekarang';
            buyNowBtn.style.pointerEvents = 'auto';
            
        } else {
            // Enable buttons
            addToCartBtn.classList.remove('disabled');
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-cart-plus"></i>';
            
            buyNowBtn.classList.remove('disabled');
            buyNowBtn.textContent = 'Beli Sekarang';
            buyNowBtn.style.pointerEvents = 'auto';
        }
        
        // Update data attributes
        addToCartBtn.setAttribute('data-stock', product.stock);
    }
    
    // Add to cart functionality with stock validation
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) return;
            
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productStock = parseInt(this.getAttribute('data-stock'));
            
            if (productStock <= 0) {
                alert('Maaf, produk ini sedang habis stok');
                return;
            }
            
            // Implementasi add to cart
            console.log('Product added to cart:', productName);
            
            // Simulasi pengurangan stock (dalam implementasi nyata, ini dilakukan di server)
            // updateStock();
        });
    });
    
    // Update stock setiap 30 detik
    setInterval(updateStock, 30000);
    
    // Update stock saat halaman pertama kali dimuat
    updateStock();
});
</script>
@endsection