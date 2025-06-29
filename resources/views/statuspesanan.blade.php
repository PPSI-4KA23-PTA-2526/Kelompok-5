<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Teh Tarhadi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #6b7c3b, #8fa653);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .tabs {
            display: flex;
            background: white;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        .tab {
            flex: 1;
            padding: 12px 20px;
            text-align: center;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-weight: 500;
        }

        .tab.active {
            background: #6b7c3b;
            color: white;
        }

        .tab:hover:not(.active) {
            background: #f0f0f0;
        }

        .orders-container {
            display: none;
        }

        .orders-container.active {
            display: block;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .order-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-id {
            font-weight: bold;
            color: #6b7c3b;
        }

        .order-date {
            color: #666;
            font-size: 14px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .order-items {
            margin: 15px 0;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .item-details {
            font-size: 14px;
            color: #666;
        }

        .item-price {
            font-weight: bold;
            color: #6b7c3b;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #6b7c3b;
        }

        .order-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #6b7c3b;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6632;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state img {
            width: 120px;
            opacity: 0.5;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .order-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .order-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
            }

            .tab {
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                🍃 Pesanan Saya
            </h1>
            <p>Pantau status pesanan teh premium Anda</p>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="showOrders('pending')">Belum Bayar</div>
            <div class="tab" onclick="showOrders('processing')">Dikemas</div>
            <div class="tab" onclick="showOrders('shipped')">Dikirim</div>
            <div class="tab" onclick="showOrders('completed')">Selesai</div>
            <div class="tab" onclick="showOrders('cancelled')">Dibatalkan</div>
        </div>

        <!-- Belum Bayar -->
        <div id="pending" class="orders-container active">
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">#TH-2025-001</div>
                        <div class="order-date">22 Juni 2025, 14:30</div>
                    </div>
                    <span class="status-badge status-pending">Belum Bayar</span>
                </div>
                <div class="order-items">
                    <div class="order-item">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iOCIgZmlsbD0iIzZiN2MzYiIvPgo8dGV4dCB4PSIzMCIgeT0iNDAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIzMCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPvCfju88L3RleHQ+Cjwvc3ZnPgo=" alt="Teh Tongji" class="item-image">
                        <div class="item-info">
                            <div class="item-name">Teh Tongji</div>
                            <div class="item-details">1x • Rp 33.725</div>
                        </div>
                        <div class="item-price">Rp 33.725</div>
                    </div>
                    <div class="order-item">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iOCIgZmlsbD0iIzZiN2MzYiIvPgo8dGV4dCB4PSIzMCIgeT0iNDAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIzMCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPvCfmu48L3RleHQ+Cjwvc3ZnPgo=" alt="Teh Dandang Merah" class="item-image">
                        <div class="item-info">
                            <div class="item-name">Teh Dandang Merah</div>
                            <div class="item-details">1x • Rp 37.000</div>
                        </div>
                        <div class="item-price">Rp 37.000</div>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="total-price">Total: Rp 100.725</div>
                    <div class="order-actions">
                        <button class="btn btn-danger" onclick="cancelOrder('TH-2025-001')">Batalkan</button>
                        <button class="btn btn-primary" onclick="payOrder('TH-2025-001')">Bayar Sekarang</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dikemas -->
        <div id="processing" class="orders-container">
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">#TH-2025-002</div>
                        <div class="order-date">21 Juni 2025, 10:15</div>
                    </div>
                    <span class="status-badge status-processing">Dikemas</span>
                </div>
                <div class="order-items">
                    <div class="order-item">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iOCIgZmlsbD0iIzZiN2MzYiIvPgo8dGV4dCB4PSIzMCIgeT0iNDAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIzMCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPvCfjbU8L3RleHQ+Cjwvc3ZnPgo=" alt="Teh Gendoe" class="item-image">
                        <div class="item-info">
                            <div class="item-name">Teh Gendoe</div>
                            <div class="item-details">2x • Rp 20.000</div>
                        </div>
                        <div class="item-price">Rp 40.000</div>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="total-price">Total: Rp 50.000</div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="viewDetails('TH-2025-002')">Lihat Detail</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dikirim -->
        <div id="shipped" class="orders-container">
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">#TH-2025-003</div>
                        <div class="order-date">20 Juni 2025, 16:45</div>
                    </div>
                    <span class="status-badge status-shipped">Dikirim</span>
                </div>
                <div class="order-items">
                    <div class="order-item">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iOCIgZmlsbD0iIzZiN2MzYiIvPgo8dGV4dCB4PSIzMCIgeT0iNDAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIzMCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPvCfju48L3RleHQ+Cjwvc3ZnPgo=" alt="Teh Tongji" class="item-image">
                        <div class="item-info">
                            <div class="item-name">Teh Tongji Premium</div>
                            <div class="item-details">3x • Rp 33.725</div>
                        </div>
                        <div class="item-price">Rp 101.175</div>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="total-price">Total: Rp 111.175</div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="trackOrder('TH-2025-003')">Lacak Paket</button>
                        <button class="btn btn-primary" onclick="confirmReceived('TH-2025-003')">Terima Pesanan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div id="completed" class="orders-container">
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">#TH-2025-004</div>
                        <div class="order-date">18 Juni 2025, 12:30</div>
                    </div>
                    <span class="status-badge status-completed">Selesai</span>
                </div>
                <div class="order-items">
                    <div class="order-item">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iOCIgZmlsbD0iIzZiN2MzYiIvPgo8dGV4dCB4PSIzMCIgeT0iNDAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIzMCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPvCfmu48L3RleHQ+Cjwvc3ZnPgo=" alt="Teh Dandang Merah" class="item-image">
                        <div class="item-info">
                            <div class="item-name">Teh Dandang Merah</div>
                            <div class="item-details">1x • Rp 37.000</div>
                        </div>
                        <div class="item-price">Rp 37.000</div>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="total-price">Total: Rp 47.000</div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="viewDetails('TH-2025-004')">Lihat Detail</button>
                        <button class="btn btn-primary" onclick="reorder('TH-2025-004')">Pesan Lagi</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dibatalkan -->
        <div id="cancelled" class="orders-container">
            <div class="empty-state">
                <div style="font-size: 60px; margin-bottom: 20px;">🚫</div>
                <h3>Tidak ada pesanan dibatalkan</h3>
                <p>Semua pesanan Anda berjalan lancar!</p>
            </div>
        </div>
    </div>

    <script>
        function showOrders(status) {
            // Hide all containers
            document.querySelectorAll('.orders-container').forEach(container => {
                container.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected container
            document.getElementById(status).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function payOrder(orderId) {
            alert(`Mengarahkan ke pembayaran untuk pesanan ${orderId}`);
            // Di sini akan ada integrasi dengan Midtrans
        }

        function cancelOrder(orderId) {
            if (confirm(`Apakah Anda yakin ingin membatalkan pesanan ${orderId}?`)) {
                alert(`Pesanan ${orderId} dibatalkan`);
                // API call to cancel order
            }
        }

        function viewDetails(orderId) {
            alert(`Menampilkan detail pesanan ${orderId}`);
        }

        function trackOrder(orderId) {
            alert(`Melacak pesanan ${orderId}`);
        }

        function confirmReceived(orderId) {
            if (confirm(`Konfirmasi bahwa Anda telah menerima pesanan ${orderId}?`)) {
                alert(`Pesanan ${orderId} dikonfirmasi diterima`);
                // API call to update order status
            }
        }

        function reorder(orderId) {
            alert(`Mengulangi pesanan ${orderId}`);
        }
    </script>
</body>
</html>