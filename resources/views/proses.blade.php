@extends('layouts.navfott')

@section('content')

  <!-- ====== Bagian Proses Pembuatan ====== -->
  <section id="proses" class="section-produk">
    <div class="container">
      <div class="section-title">
        <h2>Proses Pembuatan</h2>
        <p>Bagaimana hidangan catering kami disiapkan dengan kualitas terbaik</p>
      </div>

      <div class="proses-timeline">
        
        <div class="proses-item">
          <div class="proses-icon">
            <i class="fas fa-shopping-basket"></i>
          </div>
          <div class="proses-content">
            <h3>Pemilihan Bahan Baku</h3>
            <p>Kami memilih bahan makanan segar dan berkualitas dari supplier terpercaya untuk menjaga rasa dan kebersihan.</p>
          </div>
        </div>

        <div class="proses-item">
          <div class="proses-icon">
            <i class="fas fa-utensils"></i>
          </div>
          <div class="proses-content">
            <h3>Proses Memasak</h3>
            <p>Semua hidangan dimasak oleh tim dapur berpengalaman dengan standar kebersihan dan resep khas UMKM kami.</p>
          </div>
        </div>

        <div class="proses-item">
          <div class="proses-icon">
            <i class="fas fa-concierge-bell"></i>
          </div>
          <div class="proses-content">
            <h3>Quality Control</h3>
            <p>Setiap menu diperiksa dari segi rasa, porsi, dan tampilan agar sesuai dengan standar pelayanan catering.</p>
          </div>
        </div>

        <div class="proses-item">
          <div class="proses-icon">
            <i class="fas fa-box-open"></i>
          </div>
          <div class="proses-content">
            <h3>Pengemasan & Pengiriman</h3>
            <p>Makanan dikemas secara higienis dan dikirim tepat waktu agar tetap segar saat diterima pelanggan.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

@endsection
