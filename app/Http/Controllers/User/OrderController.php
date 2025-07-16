<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config; // Pastikan ini diimpor
use Midtrans\Snap;   // Pastikan ini diimpor
use Illuminate\Http\JsonResponse; // Pastikan ini diimpor

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan pengguna.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Mengambil pesanan untuk pengguna yang sedang login
        $ordersQuery = Order::where('user_id', $user->id)
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status jika ada parameter status dalam request
        if ($request->has('status') && $request->status !== '') {
            $ordersQuery->where('status', $request->status);
        }

        // Menerapkan paginasi
        $orders = $ordersQuery->paginate(10)->appends($request->query());

        return view('user.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan tertentu.
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        // Memastikan pesanan adalah milik pengguna yang sedang login
        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses pesanan ini.');
        }

        // Memuat relasi yang diperlukan (item pesanan dan produk terkait)
        $order->load(['orderItems.product', 'user']);

        return view('user.orders.show', compact('order'));
    }

    /**
     * Membatalkan pesanan jika memungkinkan.
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();

        // Memastikan pesanan adalah milik pengguna yang sedang login
        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses pesanan ini.');
        }

        // Hanya bisa membatalkan jika statusnya "Menunggu Pembayaran"
        if ($order->status !== Order::STATUS_WAITING_PAYMENT) {
            return redirect()->back()->with('error', 'Order tidak dapat dibatalkan karena sudah diproses atau dibatalkan sebelumnya.');
        }

        try {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => 'cancelled_by_user'
            ]);

            return redirect()->back()->with('success', 'Order berhasil dibatalkan.');
        } catch (\Exception $e) {
            Log::error('Kesalahan pembatalan pesanan: ' . $e->getMessage(), ['order_id' => $order->id, 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Gagal membatalkan order. Silakan coba lagi.');
        }
    }

    /**
     * Mengkonfirmasi penerimaan pesanan.
     */
    public function confirmReceived(Order $order)
    {
        $user = Auth::user();

        // Memastikan pesanan adalah milik pengguna yang sedang login
        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses pesanan ini.');
        }

        // Hanya bisa mengkonfirmasi jika statusnya "Dikirim"
        if ($order->status !== Order::STATUS_SHIPPED) {
            return redirect()->back()->with('error', 'Order belum dalam status dikirim.');
        }

        try {
            $order->update([
                'status' => Order::STATUS_DELIVERED,
                'completed_at' => now()
            ]);

            return redirect()->back()->with('success', 'Terima kasih! Order telah dikonfirmasi diterima.');
        } catch (\Exception $e) {
            Log::error('Kesalahan konfirmasi pesanan: ' . $e->getMessage(), ['order_id' => $order->id, 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Gagal mengkonfirmasi penerimaan order. Silakan coba lagi.');
        }
    }

    /**
     * Menghasilkan Midtrans Snap token untuk pesanan yang statusnya menunggu pembayaran.
     *
     */
    public function payOrder(Request $request, Order $order): JsonResponse
    {
        $user = Auth::user();

        // Memastikan pesanan milik pengguna yang sedang login
        if ($order->user_id !== $user->id) {
            return response()->json(['success' => false, 'error' => 'Anda tidak berhak mengakses pesanan ini.'], 403);
        }

        // Hanya izinkan pembayaran untuk pesanan dengan status 'Menunggu Pembayaran'
        if ($order->status !== Order::STATUS_WAITING_PAYMENT) {
            return response()->json(['success' => false, 'error' => 'Pesanan tidak dapat dibayar. Status saat ini: ' . $order->status], 400);
        }

        try {
            // Konfigurasi Midtrans (pastikan ini konsisten dengan CheckoutController)
            Config::$serverKey = config('services.midtrans.server_key', 'SB-Mid-server-NemakLA3BvevwqiknDVh1gB-'); // Ganti dengan server key Anda
            Config::$isProduction = config('services.midtrans.is_production', false);
            Config::$isSanitized = config('services.midtrans.is_sanitized', true);
            Config::$is3ds = config('services.midtrans.is_3ds', true);

            $customerDetails = [
                'first_name' => $order->customer_first_name,
                'last_name' => $order->customer_last_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'shipping_address' => [
                    'first_name' => $order->customer_first_name,
                    'last_name' => $order->customer_last_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'address' => $order->customer_address,
                    'city' => $order->customer_city,
                    'postal_code' => $order->customer_postal_code,
                    'country_code' => 'IDN' // Diasumsikan Indonesia
                ]
            ];

            $itemDetails = [];
            // Jika relasi orderItems dimuat, gunakan itu. Jika tidak, parse JSON 'items'.
            if ($order->relationLoaded('orderItems') && $order->orderItems->isNotEmpty()) {
                foreach ($order->orderItems as $item) {
                    $itemDetails[] = [
                        'id' => $item->product_id,
                        'name' => $item->product ? $item->product->name : 'Product',
                        'price' => (int) $item->price,
                        'quantity' => (int) $item->quantity,
                    ];
                }
            } else {
                // Fallback jika orderItems tidak dimuat atau item disimpan langsung dalam JSON 'items'
                foreach ($order->items as $item) {
                    $itemDetails[] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'price' => (int) $item['price'],
                        'quantity' => (int) $item['quantity'],
                    ];
                }
            }

            // Tambahkan biaya pengiriman sebagai item terpisah jika ada
            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id' => 'SHIPPING_COST',
                    'name' => 'Biaya Pengiriman',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number . '-' . time(), // Tambahkan timestamp agar unik
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'callbacks' => [
                    // Disarankan untuk memiliki URL finish/callback, meskipun tidak menggunakan webhook
                    'finish' => route('user.orders.show', $order->id) . '?payment_status=success',
                    'error' => route('user.orders.show', $order->id) . '?payment_status=error',
                    'pending' => route('user.orders.show', $order->id) . '?payment_status=pending',
                ]
            ];

            $snapToken = Snap::getSnapToken($params);

            // Log token Snap yang dihasilkan
            Log::info('✅ Snap Token Generated for Order:', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'snap_token' => substr($snapToken, 0, 20) . '...' // Log sebagian token untuk keamanan
            ]);

            // Perbarui ID pesanan Midtrans di database jika belum ada
            if (empty($order->midtrans_order_id)) {
                $order->update(['midtrans_order_id' => $order->order_number]);
            }

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order->id,
                'message' => 'Snap token berhasil dibuat.'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Kesalahan menghasilkan Snap Token untuk Pesanan:', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal menghasilkan token pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}