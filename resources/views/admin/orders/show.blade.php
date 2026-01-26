@extends('layouts.admin')

@section('title', 'Detail Order #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Detail Order</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
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

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">Order #{{ $order->order_number }}</h5>
                            <p class="text-muted mb-0">
                                <small>Order ID: {{ $order->order_id }}</small>
                            </p>
                            <p class="text-muted mb-0">
                                <small>Dibuat: {{ $order->created_at->format('d M Y, H:i') }}</small>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $order->getStatusBadgeClass() }} mb-2 d-block" id="status-badge">
                                {{ $order->status }}
                            </span>
                            <span class="badge {{ $order->getPaymentStatusBadgeClass() }}" id="payment-badge">
                                Payment: {{ ucfirst($order->payment_status ?? 'N/A') }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Update Form -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Update Status Order</label>
                            <div class="d-flex gap-2">
                                <select id="order-status-select" class="form-select form-select-sm">
                                    @foreach(App\Models\Order::getAvailableStatuses() as $key => $label)
                                        <option value="{{ $key }}" {{ $order->api_status == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" id="update-status-btn" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i> Update
                                </button>
                            </div>
                        </div>
                        @if($order->payment_method === 'midtrans' && $order->midtrans_order_id)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sync Payment Status</label>
                            <div>
                                <a href="{{ route('admin.orders.syncPayment', $order) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i> Sync dari Midtrans
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Produk yang Dipesan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($order->orderItems && $order->orderItems->count() > 0)
                                    @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="rounded me-2"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-medium">{{ $item->product_name }}</div>
                                                    @if($item->product)
                                                        <small class="text-muted">SKU: {{ $item->product->id }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end fw-medium">
                                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @elseif($order->items)
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $item['name'] ?? 'N/A' }}</div>
                                            <small class="text-muted">ID: {{ $item['id'] ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                                        <td class="text-end">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end fw-medium">
                                            Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            Tidak ada item
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-medium">Subtotal:</td>
                                    <td class="text-end">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-medium">Ongkos Kirim:</td>
                                    <td class="text-end">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total:</td>
                                    <td class="text-end text-primary fs-5">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tracking Information -->
            @if(in_array($order->status, ['Dikirim', 'Selesai']) || $order->tracking_number)
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if($order->shipped_at)
                        <div class="mt-3">
                            <small class="text-muted">
                                Dikirim pada: {{ $order->shipped_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Customer</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Nama</label>
                        <div class="fw-medium">{{ $order->customer_full_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Email</label>
                        <div>{{ $order->customer_email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Telepon</label>
                        <div>{{ $order->customer_phone }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Alamat</label>
                        <div>{{ $order->customer_address }}</div>
                        <div class="text-muted small">
                            {{ $order->customer_city }}, {{ $order->customer_province }} {{ $order->customer_postal_code }}
                        </div>
                    </div>
                    @if($order->customer_notes)
                    <div class="mb-0">
                        <label class="form-label text-muted small mb-1">Catatan</label>
                        <div class="alert alert-info mb-0 py-2">
                            <small>{{ $order->customer_notes }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-medium text-capitalize">
                            @if($order->payment_method === 'cod')
                                <i class="fas fa-money-bill-wave text-success me-1"></i> Cash on Delivery
                            @elseif($order->payment_method === 'mbanking')
                                <i class="fas fa-mobile-alt text-primary me-1"></i> Mobile Banking
                            @elseif($order->payment_method === 'midtrans')
                                <i class="fas fa-credit-card text-info me-1"></i> Payment Gateway
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Status Pembayaran</label>
                        <div>
                            <span class="badge {{ $order->getPaymentStatusBadgeClass() }}">
                                {{ ucfirst($order->payment_status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                    @if($order->paid_at)
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Dibayar Pada</label>
                        <div>{{ $order->paid_at->format('d M Y, H:i') }}</div>
                    </div>
                    @endif
                    @if($order->transaction_id)
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Transaction ID</label>
                        <div class="font-monospace small">{{ $order->transaction_id }}</div>
                    </div>
                    @endif
                    @if($order->midtrans_order_id)
                    <div class="mb-0">
                        <label class="form-label text-muted small mb-1">Midtrans Order ID</label>
                        <div class="font-monospace small">{{ $order->midtrans_order_id }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="fw-medium">Order Dibuat</div>
                                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        
                        @if($order->paid_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="fw-medium">Pembayaran Diterima</div>
                                <small class="text-muted">{{ $order->paid_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @endif

                        @if($order->shipped_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <div class="fw-medium">Pesanan Dikirim</div>
                                <small class="text-muted">{{ $order->shipped_at->format('d M Y, H:i') }}</small>
                                @if($order->tracking_number)
                                <div class="small mt-1">
                                    <span class="text-muted">Resi:</span> {{ $order->tracking_number }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($order->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="fw-medium">Pesanan Selesai</div>
                                <small class="text-muted">{{ $order->completed_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    padding-left: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateBtn = document.getElementById('update-status-btn');
    const statusSelect = document.getElementById('order-status-select');
    const statusBadge = document.getElementById('status-badge');
    
    if (updateBtn && statusSelect) {
        updateBtn.addEventListener('click', async function() {
            const newStatus = statusSelect.value;
            const btn = this;
            const originalHtml = btn.innerHTML;
            
            // Disable button dan show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
            
            try {
                const response = await fetch('{{ route("admin.orders.updateStatus", $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update badge
                    statusBadge.textContent = data.status_label;
                    statusBadge.className = 'badge ' + data.status_badge_class + ' mb-2 d-block';
                    
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert('danger', data.message || 'Gagal update status');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan saat update status');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endsection