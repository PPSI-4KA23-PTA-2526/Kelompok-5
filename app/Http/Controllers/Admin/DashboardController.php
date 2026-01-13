<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Periode waktu
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // STATUS INDONESIA - Sesuai dengan database Anda
        // Status yang dihitung sebagai revenue (sudah dibayar/diproses)
        $revenueStatuses = ['Dibayar', 'Diproses', 'Dikirim', 'Selesai'];
        
        // Total Products
        $totalProducts = Product::count();
        $newProductsThisMonth = Product::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        // Total Users - gunakan scope customers() jika ada, atau filter manual
        $totalUsers = User::customers()->count();
        $newUsersThisMonth = User::customers()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        
        // Orders Statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'Menunggu Pembayaran')->count();
        $completedOrders = Order::where('status', 'Selesai')->count();
        
        // Revenue - hanya hitung dari order yang sudah diproses/selesai
        $totalRevenue = Order::whereIn('status', $revenueStatuses)
            ->sum('total_amount');
        
        // TODAY'S REVENUE - Pendapatan hari ini
        $todayRevenue = Order::whereDate('created_at', $today)
            ->whereIn('status', $revenueStatuses)
            ->sum('total_amount');
        
        // YESTERDAY'S REVENUE - Untuk perhitungan growth
        $yesterdayRevenue = Order::whereDate('created_at', $yesterday)
            ->whereIn('status', $revenueStatuses)
            ->sum('total_amount');
        
        // REVENUE GROWTH - Persentase pertumbuhan dari kemarin
        $revenueGrowth = 0;
        if ($yesterdayRevenue > 0) {
            $revenueGrowth = round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1);
        } elseif ($todayRevenue > 0) {
            $revenueGrowth = 100; // Jika kemarin 0 tapi hari ini ada, maka 100% growth
        }
        
        // Products Sold - total quantity dari order yang valid
        $totalProductsSold = OrderItem::whereHas('order', function($query) use ($revenueStatuses) {
            $query->whereIn('status', $revenueStatuses);
        })->sum('quantity');
        
        // Average Order Value
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 0) : 0;
        
        // Completion Rate
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
        
        // Low Stock Products (stok kurang dari 10)
        $lowStockProducts = Product::where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->get();
        
        // Stats array
        $stats = [
            'total_products' => $totalProducts,
            'new_products_this_month' => $newProductsThisMonth,
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'revenue_growth' => $revenueGrowth,
            'total_products_sold' => $totalProductsSold,
            'average_order_value' => $averageOrderValue,
            'completion_rate' => $completionRate,
            'low_stock_products' => $lowStockProducts->count(),
        ];
        
        // Recent Orders - ambil 10 pesanan terbaru dengan relasi user
        $recent_orders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        // Top Products (produk terlaris) - ambil 5 produk dengan penjualan tertinggi
        $top_products = Product::select('products.*')
            // ->with('category') // comment jika tidak ada relasi category
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function($query) use ($revenueStatuses) {
                $query->whereIn('orders.status', $revenueStatuses)
                    ->orWhereNull('orders.status');
            })
            ->groupBy('products.id')
            ->selectRaw('products.*, COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->selectRaw('COALESCE(SUM(order_items.quantity * order_items.price), 0) as total_revenue')
            ->orderByDesc('total_sold')
            ->having('total_sold', '>', 0)
            ->take(5)
            ->get();
        
        // Chart Data - Penjualan 7 hari terakhir
        $chart_labels = [];
        $chart_data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chart_labels[] = $date->format('d M');
            
            $dailyRevenue = Order::whereDate('created_at', $date)
                ->whereIn('status', $revenueStatuses)
                ->sum('total_amount');
            
            $chart_data[] = (float) $dailyRevenue;
        }
        
        // Order Status Data untuk Doughnut Chart - GUNAKAN STATUS INDONESIA
        $status_labels = ['Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
        $status_data = [
            Order::where('status', 'Menunggu Pembayaran')->count(),
            Order::where('status', 'Dibayar')->count(),
            Order::where('status', 'Diproses')->count(),
            Order::where('status', 'Dikirim')->count(),
            Order::where('status', 'Selesai')->count(),
            Order::where('status', 'Dibatalkan')->count(),
        ];
        
        // Recent Activities (optional - jika tidak ada tabel activities, bisa dikosongkan)
        $recent_activities = [];
        
        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'top_products',
            'chart_labels',
            'chart_data',
            'status_labels',
            'status_data',
            'recent_activities',
            'lowStockProducts'
        ));
    }
}