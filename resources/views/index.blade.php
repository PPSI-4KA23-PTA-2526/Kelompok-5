@extends('layouts.navfott')

@section('content')

  <!-- ====== Bagian Hero ====== -->
  <section id="hero"
    style="background: url('image/bg.png') no-repeat center center/cover;   height: 115vh; color: var(--white-color); display: flex; align-items: center;">
    <div class="container">
      <div class="hero-content">
        <h2>Jangan Mau Masak Sendiri Pesan DiKami Saja</h2>
        <a href="/produk" class="btn">Lihat Produk Kami</a>
      </div>
    </div>
  </section>

  <!-- ====== Bagian Fitur ====== -->
  <section id="fitur" class="section-padding">
    <div class="container">
      <div class="fitur-grid">
        <div class="fitur-item">
          <i class="fas fa-leaf"></i>
          <h3>100% Bahan Segar</h3>
          <p>Tanpa bahan pengawet dan menggunakan bahan segar pilihan setiap hari</p>
        </div>
        <div class="fitur-item">
          <i class="fas fa-medal"></i>
          <h3>Kualitas Terbaik</h3>
          <p>Diolah dari bahan berkualitas dengan standar kebersihan terjaga</p>
        </div>
        <div class="fitur-item">
          <i class="fas fa-history"></i>
          <h3>Resep Sendiri</h3>
          <p>Dimasak dengan resep andalan keluarga yang terjaga cita rasanya</p>
        </div>
        <div class="fitur-item">
          <i class="fa-solid fa-money-check-dollar"></i>
          <h3>Harga Terjangkau</h3>
          <p>Paket catering lezat dengan harga ramah untuk berbagai acara</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== Bagian produk baru ====== -->
  <section id="produk" class="section-padding bg-light">
    <div class="container">
      <div class="section-title">
        <h2>Produk Baru Dari Kita</h2>
        <p>Menu Catering Baru</p>
      </div>
      <div class="newproduk-grid">
        <div class="newproduk-item">
          <div class="newproduk-content">
            <div class="newproduk-image">
              <img src="image/produknew.png" alt="Logo Catering Mama Zel">
            </div>
            <div class="newproduk-text">
              <div class="new-info">
                <h4>Menu Catering Baru</h4>
              </div>
              <p>"Menu catering terbaru dari Mama Zel kini hadir dengan pilihan hidangan yang lebih variatif dan dapat
                disesuaikan untuk berbagai kebutuhan acara, mulai dari arisan, rapat kantor, hingga pesta keluarga.
                Setiap menu diolah menggunakan bahan segar dengan cita rasa yang terjaga, menghadirkan kombinasi
                pilihan lauk Nusantara hingga hidangan kekinian yang sedang diminati. Dengan opsi paket yang fleksibel
                dan porsi yang dapat menyesuaikan anggaran, Mama Zel berkomitmen memberikan pengalaman kuliner
                yang lezat serta menjadikan setiap acara terasa lebih berkesan."</p>
              <button class="btn add-to-cart" data-id="1" data-name="Paket Menu Ayam Bakar" data-price="25000"
                data-img="image/produknew.png">
                <i class="fas fa-cart-plus"></i> Tambahkan ke Keranjang
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  </div>
  </section>

@endsection