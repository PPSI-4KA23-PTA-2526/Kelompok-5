@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders Management')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Orders List</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="waiting_payment">Menunggu Pembayaran</option>
                    <option value="paid">Dibayar</option>
                    <option value="processing">Diproses</option>
                    <option value="shipped">Dikirim</option>
                    <option value="delivered">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="refunded">Dikembalikan</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        @foreach($orders as $order)
                            <tr data-order-id="{{ $order->id }}" data-status="{{ $order->status }}">
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    @if($order->order_id)
                                        <br><small class="text-muted">MT: {{ $order->order_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->user ? $order->user->name : $order->customer_first_name . ' ' . $order->customer_last_name }}</strong>
                                        @if($order->customer_email)
                                            <br><small class="text-muted">{{ $order->customer_email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle status-btn" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false"
                                            style="background: none; border: none; padding: 0;">
                                            <span class="badge {{ $order->getStatusBadgeClass() }} status-badge">
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <h6 class="dropdown-header">Update Status</h6>
                                            </li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="waiting_payment">
                                                    <span class="badge bg-warning">Menunggu Pembayaran</span>
                                                </a></li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="paid">
                                                    <span class="badge bg-info">Dibayar</span>
                                                </a></li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="processing">
                                                    <span class="badge bg-primary">Diproses</span>
                                                </a></li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="shipped">
                                                    <span class="badge" style="background-color: #6f42c1;">Dikirim</span>
                                                </a></li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="delivered">
                                                    <span class="badge bg-success">Selesai</span>
                                                </a></li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="cancelled">
                                                    <span class="badge bg-danger\\\">Dibatalkan</span>
                                                </a></li>
                                            <li><a class="dropdown-item update-status" href="#" data-order-id="{{ $order->id }}"
                                                    data-status="refunded">
                                                    <span class="badge bg-secondary">Dikembalikan</span>
                                                </a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->created_at->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-danger" onclick="deleteOrder({{ $order->id }})"
                                            title="Delete Order">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($orders->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada order</p>
                </div>
            @endif
        </div>

        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Updating status...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF token not found');
            }

            // Update Status (unified function)
            document.querySelectorAll('.update-status').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    const orderId = this.getAttribute('data-order-id');
                    const status = this.getAttribute('data-status');
                    const statusLabel = this.querySelector('.badge').textContent.trim();

                    if (!confirm(`Update status ke "${statusLabel}"?`)) {
                        return;
                    }

                    updateOrderStatus(orderId, status);
                });
            });

            // Status Filter
            document.getElementById('statusFilter').addEventListener('change', function () {
                const selectedStatus = this.value;
                const rows = document.querySelectorAll('#ordersTableBody tr');

                rows.forEach(function (row) {
                    if (selectedStatus === '' || row.getAttribute('data-status') === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            function showLoading() {
                const modal = document.getElementById('loadingModal');
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }

            function hideLoading() {
                const modal = document.getElementById('loadingModal');
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }

            function updateOrderStatus(orderId, status) {
                showLoading();

                fetch(`/admin/orders/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();

                        if (data.success) {
                            // Update status badge
                            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                            const statusBadge = row.querySelector('.status-badge');

                            statusBadge.textContent = data.status_label;
                            statusBadge.className = `badge status-badge ${data.status_badge_class}`;

                            // Update row data attribute
                            row.setAttribute('data-status', data.status);

                            showAlert('success', data.message);
                        } else {
                            showAlert('error', data.message);
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        showAlert('error', 'Terjadi kesalahan saat mengupdate status');
                    });
            }

            function showAlert(type, message) {
                // Remove existing alerts
                document.querySelectorAll('.alert').forEach(alert => alert.remove());

                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

                const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${iconClass} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

                const cardBody = document.querySelector('.card-body');
                cardBody.insertAdjacentHTML('afterbegin', alertHtml);

                // Auto hide after 5 seconds
                setTimeout(() => {
                    const alert = document.querySelector('.alert');
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            }
        });

        function deleteOrder(orderId) {
            if (!confirm('Apakah Anda yakin ingin menghapus order ini?')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/orders/${orderId}`;

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            form.appendChild(methodField);
            form.appendChild(csrfField);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush