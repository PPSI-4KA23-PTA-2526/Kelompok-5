@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('user.orders.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Pesanan
                </a>
                <h1 class="h3 text-primary mb-1">Detail Pesanan #{{ $order->order_number }}</h1>
                <p class="text-muted mb-0">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div class="d-flex gap-2">
                @php
                    $statusConfig = [
                        'Menunggu Pembayaran' => ['icon' => 'fa-clock', 'class' => 'warning'],
                        'Dibayar' => ['icon' => 'fa-credit-card', 'class' => 'info'],
                        'Diproses' => ['icon' => 'fa-cogs', 'class' => 'info'],
                        'Dikirim' => ['icon' => 'fa-shipping-fast', 'class' => 'primary'],
                        'Selesai' => ['icon' => 'fa-check-circle', 'class' => 'success'],
                        'Dibatalkan' => ['icon' => 'fa-times-circle', 'class' => 'danger'],
                        'Dikembalikan' => ['icon' => 'fa-undo', 'class' => 'secondary']
                    ];
                    $config = $statusConfig[$order->status] ?? ['icon' => 'fa-question', 'class' => 'secondary'];
                @endphp
                <button class="btn btn-success btn-sm" onclick="printInvoice()">
                    <i class="fas fa-print me-1"></i>Cetak Invoice
                </button>
                <span class="badge bg-{{ $config['class'] }} fs-6 px-3 py-2">
                    <i class="fas {{ $config['icon'] }} me-1"></i>{{ $order->status }}
                </span>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Invoice Section (Hidden, for printing) -->
        <div id="invoice-section" class="d-none">
            <div class="invoice-container">
                <!-- Invoice Header -->
                <div class="invoice-header text-center mb-4">
                    <img src="{{ asset('image/logo1.png') }}" alt="Logo" style="height: 60px;" class="mb-2">
                    <h2 class="text-success mb-1">Catering Mama Zel</h2>
                    <p class="text-muted mb-0">Jl. Puskesmas No.40, RT.2/RW.6, Klp. Gading Tim., Kec. Klp. Gading, Jkt
                        Utara, Daerah Khusus Ibukota Jakarta 14240 | Telp: 081293430440</p>
                    <hr class="my-3">
                    <h3 class="text-dark">INVOICE</h3>
                </div>

                <!-- Invoice Info -->
                <div class="row mb-4">
                    <div class="col-6">
                        <strong>Kepada:</strong><br>
                        {{ $order->user->name ?? 'Customer' }}<br>
                        {{ $order->user->email ?? '' }}<br>
                        {{ $order->shipping_address ?? '' }}
                    </div>
                    <div class="col-6 text-end">
                        <strong>No. Invoice:</strong> {{ $order->order_number }}<br>
                        <strong>Tanggal:</strong> {{ $order->created_at->format('d F Y') }}<br>
                        <strong>Status:</strong> <span
                            class="badge bg-{{ $config['class'] }}">{{ $order->status }}</span><br>
                        @if($order->paid_at)
                            <strong>Tgl Bayar:</strong> {{ $order->paid_at->format('d F Y') }}
                        @endif
                    </div>
                </div>

                <!-- Invoice Items -->
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product ? $item->product->name : 'Product Not Found' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Invoice Footer -->
                <div class="row mt-4">
                    <div class="col-6">
                        <p><strong>Catatan:</strong></p>
                        <p>Terima kasih Atas Pembelian Anda.</p>
                    </div>
                    <div class="col-12 text-end">
                        <p>Jakarta, {{ now()->format('d F Y') }}</p>
                        <br><br><br>
                        <p><strong>Catering Mama Zel</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Order Details -->
            <div class="col-lg-8">
                <!-- Order Timeline -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-timeline me-2 text-primary"></i>Timeline Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <!-- Order Created -->
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pesanan Dibuat</h6>
                                    <p class="text-muted mb-0">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                                </div>
                            </div>

                            <!-- Payment -->
                            <div
                                class="timeline-item {{ in_array($order->status, ['Dibayar', 'Diproses', 'Dikirim', 'Selesai']) ? 'active' : '' }}">
                                <div
                                    class="timeline-marker {{ in_array($order->status, ['Dibayar', 'Diproses', 'Dikirim', 'Selesai']) ? 'bg-success' : 'bg-secondary' }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pembayaran</h6>
                                    @if($order->paid_at)
                                        <p class="text-success mb-0">Dibayar pada {{ $order->paid_at->format('d F Y, H:i') }}
                                            WIB</p>
                                    @else
                                        <p class="text-muted mb-0">Menunggu pembayaran</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Processing -->
                            <div
                                class="timeline-item {{ in_array($order->status, ['Diproses', 'Dikirim', 'Selesai']) ? 'active' : '' }}">
                                <div
                                    class="timeline-marker {{ in_array($order->status, ['Diproses', 'Dikirim', 'Selesai']) ? 'bg-success' : 'bg-secondary' }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Diproses</h6>
                                    <p class="text-muted mb-0">Pesanan sedang diproses</p>
                                </div>
                            </div>

                            <!-- Shipped -->
                            <div
                                class="timeline-item {{ in_array($order->status, ['Dikirim', 'Selesai']) ? 'active' : '' }}">
                                <div
                                    class="timeline-marker {{ in_array($order->status, ['Dikirim', 'Selesai']) ? 'bg-success' : 'bg-secondary' }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Dikirim</h6>
                                    @if($order->shipped_at)
                                        <p class="text-success mb-0">Dikirim pada {{ $order->shipped_at->format('d F Y, H:i') }}
                                            WIB</p>
                                        @if($order->tracking_number)
                                            <small class="text-muted">No. Resi: {{ $order->tracking_number }}</small>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">Belum dikirim</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Completed -->
                            <div class="timeline-item {{ $order->status === 'Selesai' ? 'active' : '' }}">
                                <div
                                    class="timeline-marker {{ $order->status === 'Selesai' ? 'bg-success' : 'bg-secondary' }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Selesai</h6>
                                    @if($order->completed_at)
                                        <p class="text-success mb-0">Selesai pada
                                            {{ $order->completed_at->format('d F Y, H:i') }} WIB</p>
                                    @else
                                        <p class="text-muted mb-0">Belum selesai</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-bag me-2 text-primary"></i>Data Pesanan</h5>
                            <button class="btn btn-outline-primary btn-sm" onclick="viewInvoice()">
                                <i class="fas fa-file-invoice me-1"></i>Lihat Invoice
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                    @if($order->orderItems->count() > 0)
                        @foreach($order->orderItems as $item)
                        <div class="border-bottom p-4">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="img-fluid rounded" style="max-height: 80px;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <h6 class="mb-1">{{ $item->product ? $item->product->name : 'Product Not Found' }}</h6>
                                    @if($item->product && $item->product->description)
                                        <small class="text-muted">{{ Str::limit($item->product->description, 60) }}</small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-light text-dark border">{{ $item->quantity }} pcs</span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="text-muted small">@ Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted">Tidak ada item dalam pesanan ini</p>
                        </div>
                    @endif
                </div>
                </div>
            </div>

            <!-- Right Column - Order Summary & Actions -->
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2 text-primary"></i>Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6"><strong>No. Pesanan:</strong></div>
                            <div class="col-6 text-end">{{ $order->order_number }}</div>
                        </div>
                        @if($order->order_id)
                            <div class="row mb-3">
                                <div class="col-6"><strong>Order ID:</strong></div>
                                <div class="col-6 text-end">{{ $order->order_id }}</div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-6"><strong>Tanggal:</strong></div>
                            <div class="col-6 text-end">{{ $order->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6"><strong>Status:</strong></div>
                            <div class="col-6 text-end">
                                <span class="badge bg-{{ $config['class'] }}">{{ $order->status }}</span>
                            </div>
                        </div>
                        @if($order->payment_status)
                            <div class="row mb-3">
                                <div class="col-6"><strong>Pembayaran:</strong></div>
                                <div class="col-6 text-end">
                                    <span class="badge bg-light text-dark border">{{ ucfirst($order->payment_status) }}</span>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <div class="row">
                            <div class="col-6"><strong>Total Bayar:</strong></div>
                            <div class="col-6 text-end">
                                <h5 class="text-success mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($order->status === 'Menunggu Pembayaran' && $order->order_id)
                            <button id="pay-button" class="btn btn-warning" data-order-id="{{ $order->id }}">
                                <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                </button>
                            @endif


                            @if($order->status === 'Menunggu Pembayaran')
                                <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-times me-2"></i>Batalkan Pesanan
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'Dikirim')
                                <form action="{{ route('user.orders.confirm-received', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Konfirmasi bahwa Anda telah menerima pesanan ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>Konfirmasi Diterima
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'Selesai')
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-check-circle me-2"></i>Pesanan Selesai
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Invoice #{{ $order->order_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-invoice-content">
                    <!-- Invoice content akan dimuat di sini -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" onclick="printInvoice()">
                        <i class="fas fa-print me-1"></i>Cetak Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .timeline {
                position: relative;
                padding-left: 30px;
            }

            .timeline::before {
                content: '';
                position: absolute;
                left: 10px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #e9ecef;
            }

            .timeline-item {
                position: relative;
                margin-bottom: 30px;
            }

            .timeline-item.active .timeline-content h6 {
                color: #28a745;
            }

            .timeline-marker {
                position: absolute;
                left: -25px;
                top: 0;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 3px solid #fff;
                box-shadow: 0 0 0 2px #e9ecef;
            }

            .timeline-item.active .timeline-marker {
                box-shadow: 0 0 0 2px #28a745;
            }

            .timeline-content {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                border-left: 3px solid #e9ecef;
            }

            .timeline-item.active .timeline-content {
                border-left-color: #28a745;
                background: #f8fff9;
            }

            .card {
                border-radius: 12px;
            }

            .badge {
                font-size: 0.85em;
            }

            .invoice-container {
                background: white;
                padding: 40px;
                font-family: Arial, sans-serif;
            }

            .invoice-header {
                border-bottom: 2px solid #007bff;
                padding-bottom: 20px;
            }

            @media print {
                body * {
                    visibility: hidden;
                }

                #invoice-section,
                #invoice-section * {
                    visibility: visible;
                }

                #invoice-section {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                .d-none {
                    display: block !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

        <script>
            function viewInvoice() {
                try {
                    // Clone invoice content ke modal
                    const invoiceContent = document.getElementById('invoice-section').innerHTML;
                    const modalContent = document.getElementById('modal-invoice-content');

                    if (modalContent) {
                        modalContent.innerHTML = invoiceContent;

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                        modal.show();
                    }
                } catch (error) {
                    console.error('Error displaying invoice:', error);
                    alert('Terjadi kesalahan saat menampilkan invoice');
                }
            }

            function printInvoice() {
                try {
                    // Show invoice section temporarily
                    const invoiceSection = document.getElementById('invoice-section');
                    if (invoiceSection) {
                        invoiceSection.classList.remove('d-none');

                        // Print
                        window.print();

                        // Hide invoice section again
                        setTimeout(() => {
                            invoiceSection.classList.add('d-none');
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Error printing invoice:', error);
                    alert('Terjadi kesalahan saat mencetak invoice');
                }
            }
            document.getElementById('pay-button')?.addEventListener('click', async function () {
    const orderId = this.dataset.orderId;

    try {
        const response = await fetch(`/my-orders/${orderId}/pay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            window.snap.pay(data.snap_token, {
                onSuccess: function (result) {
                    console.log('Payment success:', result);
                    window.location.reload();
                },
                onPending: function (result) {
                    console.log('Payment pending:', result);
                    window.location.reload();
                },
                onError: function (result) {
                    console.error('Payment error:', result);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                }
            });
        } else {
            alert(data.error || 'Gagal memproses pembayaran.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghubungi server.');
    }
});
        </script>
    @endpush
@endsection