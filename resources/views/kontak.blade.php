@extends('layouts.navfott')

@section('content')

  <!-- ====== Bagian Kontak ====== -->
  <section id="kontak" class="section-produk">
    <div class="container">
      <div class="section-title">
        <h2>Hubungi Kami</h2>
        <p>Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan</p>
      </div>
      <div class="kontak-container">
        <div class="kontak-info">
          <div class="kontak-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
              <h3>Alamat</h3>
              <p>Kebagusan Rt05/Rw03, Pasar Minggu, Jakarta Selatan</p>
            </div>
          </div>
          <div class="kontak-item">
            <i class="fas fa-phone"></i>
            <div>
              <h3>Telepon</h3>
              <p>+62 855-91111-068</p>
            </div>
          </div>
          <div class="kontak-item">
            <i class="fas fa-envelope"></i>
            <div>
              <h3>Email</h3>
              <p>info@CateringMamaZel.com</p>
            </div>
          </div>
          <div class="kontak-item">
            <i class="fas fa-clock"></i>
            <div>
              <h3>Jam Operasional</h3>
              <p>Senin - Sabtu: 08.00 - 21.00</p>
            </div>
          </div>
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d491.2192131697671!2d106.90572451515186!3d-6.179270626816171!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sid!2sid!4v1746812355756!5m2!1sid!2sid"
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="kontak-form">
          <form action="{{ route('kontak.kirim') }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="nama">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="telepon">Nomor Telepon</label>
              <input type="tel" id="telepon" name="telepon">
            </div>
            <div class="form-group">
              <label for="pesan">Pesan</label>
              <textarea id="pesan" name="pesan" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Kirim Pesan</button>

            @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
      @endif
          </form>

        </div>
      </div>
    </div>
  </section>

@endsection