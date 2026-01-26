@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

    <!-- Header Section -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                data-bs-target="#filterModal">
                <i class="bi bi-funnel"></i> Filter Tanggal
            </button>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Berdasarkan Tanggal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="modal-body">
                        <!-- Quick Filter Buttons -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Filter Cepat:</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary quick-filter" data-days="0">Hari
                                    Ini</button>
                                <button type="button" class="btn btn-outline-primary quick-filter" data-days="7">7
                                    Hari</button>
                                <button type="button" class="btn btn-outline-primary quick-filter" data-days="30">30
                                    Hari</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ request('start_date', date('Y-m-01')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ request('end_date', date('Y-m-d')) }}">
                            </div>
                        </div>

                        @if(request('start_date') || request('end_date'))
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i>
                                Menampilkan data dari
                                <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}</strong>
                                sampai
                                <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Products -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Produk</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_products']) }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> Aktif
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-box-seam text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Pengguna</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> Aktif
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Pesanan</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</h3>
                            <small class="text-info">
                                <i class="bi bi-graph-up"></i> Sepanjang waktu
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-cart-check text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pesanan Tertunda</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['pending_orders']) }}</h3>
                            <small class="text-danger">
                                <i class="bi bi-clock-history"></i> Perlu Dicek
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-hourglass-split text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm chart-card">
                <div class="card-header border-0 py-3">
                    <h5 class="mb-0 fw-bold text-black">Grafik Omzet Harian</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-3">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Pesanan Terbaru</h5>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">No. Pesanan</th>
                                    <th class="border-0">Pelanggan</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_orders as $order)
                                    <tr>
                                        <td class="align-middle">
                                            <strong class="text-primary">#{{ $order->order_number }}</strong>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $order->customer_full_name }}</div>
                                                    <small class="text-muted">
                                                        {{ $order->customer_email ?? $order->user?->email ?? '-' }}
                                                    </small>

                                                </div>
                                        </td>
                                        <td class="align-middle">
                                            <small>{{ $order->created_at->format('d M Y') }}</small><br>
                                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </td>
                                        <td class="align-middle">
                                            <strong>Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Belum ada pesanan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats & Actions -->
        <div class="col-lg-4">
            <!-- Revenue Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="mb-3 fw-bold">Pendapatan Hari Ini</h6>
                    <h3 class="mb-0 fw-bold text-success">
                        Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}
                    </h3>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ min($stats['revenue_growth'] ?? 0, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Produk Baru
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-list-check me-2"></i> Kelola Pesanan
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-people me-2"></i> Lihat Pelanggan
                        </a>
                        @if(Route::has('admin.reports'))
                            <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-graph-up me-2"></i> Lihat Laporan
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            @if(isset($low_stock_products) && $low_stock_products->count() > 0)
                <div class="card border-0 shadow-sm border-start border-danger border-4">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($low_stock_products->take(5) as $product)
                                <div class="list-group-item px-0 border-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="fw-medium">{{ $product->name }}</small><br>
                                        <small class="text-danger">Stok: {{ $product->stock }}</small>
                                    </div>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-danger"
                                        title="Edit Produk">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Bootstrap Icons if not already included -->
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <style>
            .avatar-sm {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }

            .card {
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            }

            .table-hover tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.02);
            }

            /* Chart Card Gradient */
            .chart-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                overflow: hidden;
            }

            .chart-card .card-header {
                background: transparent !important;
            }

            .chart-card .card-body {
                background: white;
                border-radius: 1rem 1rem 0 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Tunggu Chart.js loaded
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js belum ter-load!');
                    return;
                }

                console.log('Chart.js version:', Chart.version);

                // Revenue Chart - Menggunakan data dari controller
                const ctx = document.getElementById('revenueChart');
                if (!ctx) {
                    console.error('Canvas revenueChart tidak ditemukan!');
                    return;
                }

                // Data dari controller
                let labels = @json($chart_labels ?? []);
                let data = @json($chart_data ?? []);

                // Debug: Cek data yang diterima
                console.log('Chart Labels:', labels);
                console.log('Chart Data:', data);

                // Jika data kosong, gunakan data dummy untuk testing
                if (!labels || labels.length === 0 || !data || data.length === 0) {
                    console.warn('Data grafik kosong, menggunakan data dummy');
                    labels = [];
                    data = [];

                    for (let i = 6; i >= 0; i--) {
                        const date = new Date();
                        date.setDate(date.getDate() - i);
                        labels.push(date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
                        data.push(Math.floor(Math.random() * 5000000) + 2000000);
                    }

                    console.log('Dummy Labels:', labels);
                    console.log('Dummy Data:', data);
                }

                try {
                    const revenueChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Omzet Harian',
                                data: data,
                                borderColor: 'rgb(99, 179, 237)',
                                backgroundColor: 'rgba(99, 179, 237, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                                pointBackgroundColor: 'rgb(99, 179, 237)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 3,
                                pointHoverBackgroundColor: 'rgb(66, 153, 225)',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        color: '#64748b',
                                        font: {
                                            size: 13,
                                            weight: '500'
                                        },
                                        padding: 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    titleColor: '#1e293b',
                                    bodyColor: '#475569',
                                    borderColor: 'rgba(99, 179, 237, 0.3)',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: true,
                                    boxPadding: 6,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: function (context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#94a3b8',
                                        font: {
                                            size: 12
                                        },
                                        callback: function (value) {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                            }
                                            return 'Rp ' + value;
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(148, 163, 184, 0.1)',
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#94a3b8',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    console.log('Chart berhasil dibuat!', revenueChart);
                } catch (error) {
                    console.error('Error membuat chart:', error);
                }


                // Quick Filter Buttons
                document.querySelectorAll('.quick-filter').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const days = parseInt(this.dataset.days);
                        const today = new Date();
                        const startDate = new Date();

                        if (days === 0) {
                            // Hari ini
                            startDate.setDate(today.getDate());
                        } else {
                            // X hari yang lalu
                            startDate.setDate(today.getDate() - days);
                        }

                        document.getElementById('start_date').value = formatDate(startDate);
                        document.getElementById('end_date').value = formatDate(today);
                    });
                });

                // Format date to YYYY-MM-DD
                function formatDate(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
            });
        </script>
    @endpush

@endsection