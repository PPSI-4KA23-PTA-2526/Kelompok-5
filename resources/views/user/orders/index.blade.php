@extends('layouts.app')

@section('title', 'Status Pesanan')

@section('content')
    <div class="container py-4">
        <div class="text-center mb-5">
            <h1 class="h3 text-dark mb-2">Pesanan Saya</h1>
        </div>

        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag fa-3x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Pesanan</h4>
                <p class="text-muted mb-4">Mulai berbelanja sekarang!</p>
                <a href="/produk" class="btn btn-primary px-4">Lihat Produk</a>
            </div>
        @else
            <div class="row mb-4">
                <div class="col-md-4 mx-auto">
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                        <option value="Dibayar">Dibayar</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Dikirim">Dikirim</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="row" id="ordersContainer">
                @foreach ($orders as $order)
                    <div class="col-md-6 col-lg-4 mb-4 order-item" data-status="{{ $order->status }}">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="card-title mb-0 text-dark">{{ $order->order_number }}</h6>
                                    @php
                                        $statusColors = [
                                            'Menunggu Pembayaran' => 'warning',
                                            'Dibayar' => 'success',
                                            'Diproses' => 'info',
                                            'Dikirim' => 'primary',
                                            'Selesai' => 'success',
                                            'Dibatalkan' => 'danger'
                                        ];
                                        $color = $statusColors[$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $order->status }}</span>
                                </div>

                                <div class="mb-3">
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                    @if($order->tracking_number && in_array($order->status, ['Dikirim', 'Selesai']))
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-truck me-1"></i>
                                            {{ $order->tracking_number }}
                                        </p>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <h5 class="text-success mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                    {{-- Tombol Bayar Sekarang --}}
                                    @if($order->status === 'Menunggu Pembayaran')
                                        <button class="btn btn-warning btn-sm pay-now-btn" data-order-id="{{ $order->id }}">
                                            <i class="fas fa-credit-card me-1"></i> Bayar Sekarang
                                        </button>
                                    @endif
                                    {{-- Form Pembatalan Pesanan --}}
                                    @if($order->canBeCancelled())
                                        <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                <i class="fas fa-times-circle me-1"></i> Batalkan Pesanan
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Tombol Konfirmasi Penerimaan --}}
                                    @if($order->status === 'Dikirim')
                                        <form action="{{ route('user.orders.confirmReceived', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm w-100"
                                                onclick="return confirm('Konfirmasi bahwa pesanan ini telah Anda terima?');">
                                                <i class="fas fa-check-circle me-1"></i> Konfirmasi Diterima
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        {{-- Script Midtrans Snap (pastikan data-client-key sesuai dengan kunci Anda) --}}
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key', 'SB-Mid-client-K7aO5wmtnKpu8KaH') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const payNowButtons = document.querySelectorAll('.pay-now-btn');

                payNowButtons.forEach(button => {
                    button.addEventListener('click', async function (event) {
                        event.preventDefault();

                        const orderId = this.dataset.orderId;
                        const originalButtonText = this.innerHTML;
                        const payButton = this; // Simpan referensi ke tombol

                        // Nonaktifkan tombol dan tampilkan status loading
                        payButton.disabled = true;
                        payButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...';

                        try {
                            const response = await fetch(`/my-orders/${orderId}/pay`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                            });


                            const data = await response.json();

                            if (data.success) {
                                // Buka popup Midtrans Snap
                                window.snap.pay(data.snap_token, {
                                    onSuccess: function (result) {
                                        /* Anda dapat menambahkan implementasi Anda sendiri di sini */
                                        showSuccess("Pembayaran Berhasil! Silakan cek detail pesanan Anda.");
                                        console.log('Payment success:', result);
                                        // Opsional: Redirect atau refresh halaman untuk memperbarui status pesanan
                                        window.location.reload(); // Refresh sederhana untuk menampilkan status terbaru
                                    },
                                    onPending: function (result) {
                                        /* Anda dapat menambahkan implementasi Anda sendiri di sini */
                                        showError("Pembayaran tertunda. Mohon selesaikan pembayaran Anda.");
                                        console.log('Payment pending:', result);
                                        // Opsional: Redirect atau refresh halaman
                                        window.location.reload();
                                    },
                                    onError: function (result) {
                                        /* Anda dapat menambahkan implementasi Anda sendiri di sini */
                                        showError("Pembayaran gagal. Silakan coba lagi.");
                                        console.error('Payment error:', result);
                                        // Aktifkan kembali tombol jika ada error
                                        payButton.disabled = false;
                                        payButton.innerHTML = originalButtonText;
                                    },
                                    onClose: function () {
                                        /* Anda dapat menambahkan implementasi Anda sendiri di sini */
                                        console.log('Pelanggan menutup popup tanpa menyelesaikan pembayaran');
                                        showError("Anda menutup popup pembayaran tanpa menyelesaikan transaksi.");
                                        // Aktifkan kembali tombol jika popup ditutup
                                        payButton.disabled = false;
                                        payButton.innerHTML = originalButtonText;
                                    }
                                });
                            } else {
                                showError(data.error || 'Terjadi kesalahan saat memproses pembayaran.');
                                console.error('Backend error:', data.error);
                                // Aktifkan kembali tombol jika ada error dari backend
                                payButton.disabled = false;
                                payButton.innerHTML = originalButtonText;
                            }
                        } catch (error) {
                            showError('Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
                            console.error('Fetch error:', error);
                            // Aktifkan kembali tombol jika ada error jaringan/server
                            payButton.disabled = false;
                            payButton.innerHTML = originalButtonText;
                        }
                    });
                });

                // Fungsi untuk menampilkan pesan error (diambil dari checkout.blade.php)
                function showError(message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

                    const container = document.querySelector('.container');
                    if (container) {
                        container.insertBefore(alertDiv, container.firstChild);
                    } else {
                        document.body.prepend(alertDiv); // Fallback jika .container tidak ditemukan
                    }

                    // Hapus otomatis setelah 5 detik
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }

                // Fungsi untuk menampilkan pesan sukses (diambil dari checkout.blade.php)
                function showSuccess(message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

                    const container = document.querySelector('.container');
                    if (container) {
                        container.insertBefore(alertDiv, container.firstChild);
                    } else {
                        document.body.prepend(alertDiv); // Fallback jika .container tidak ditemukan
                    }

                    // Hapus otomatis setelah 5 detik
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
            });
        </script>
    @endpush
@endsection