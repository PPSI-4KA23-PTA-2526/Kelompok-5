<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status via AJAX - Fixed untuk handle Indonesian status
     */
    public function updateStatusAjax(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'status' => 'required|string|in:waiting_payment,paid,processing,shipped,delivered,cancelled,refunded,failed'
            ]);

            $oldStatus = $order->status;
            $newStatusEnglish = $request->status;

            // Convert English status ke Indonesian menggunakan mapping
            $newStatusIndonesian = Order::STATUS_MAPPING[$newStatusEnglish] ?? $newStatusEnglish;

            // Update the order status dengan format Indonesian
            $order->update(['status' => $newStatusIndonesian]);

            // Update timestamps dan payment status berdasarkan status baru
            $this->updateOrderTimestamps($order, $newStatusEnglish);

            DB::commit();

            // Log the status change
            Log::info("Order {$order->order_number} status changed from {$oldStatus} to {$newStatusIndonesian}", [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatusIndonesian,
                'updated_by' => auth()->user()->name ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status berhasil diubah ke {$order->getStatusLabel()}",
                'status' => $newStatusEnglish, // Return English format for frontend
                'status_label' => $order->getStatusLabel(),
                'status_badge_class' => $order->getStatusBadgeClass(),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->validator->errors()->all())
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Failed to update order status via AJAX", [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'status' => $request->status,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk update timestamps berdasarkan status
     */
    private function updateOrderTimestamps(Order $order, string $status)
    {
        $updates = [];

        switch ($status) {
            case 'paid':
                if (!$order->paid_at) {
                    $updates['paid_at'] = now();
                }
                if ($order->payment_status === 'pending') {
                    $updates['payment_status'] = 'settlement';
                }
                break;

            case 'processing':
                if (!$order->paid_at) {
                    $updates['paid_at'] = now();
                }
                if ($order->payment_status === 'pending') {
                    $updates['payment_status'] = 'settlement';
                }
                break;

            case 'shipped':
                if (!$order->shipped_at) {
                    $updates['shipped_at'] = now();
                }
                break;

            case 'delivered':
                if (!$order->completed_at) {
                    $updates['completed_at'] = now();
                }
                break;

            case 'cancelled':
                if (in_array($order->payment_status, ['pending'])) {
                    $updates['payment_status'] = 'cancel';
                }
                break;

            case 'waiting_payment':
                $updates['payment_status'] = 'pending';
                $updates['paid_at'] = null;
                $updates['shipped_at'] = null;
                $updates['completed_at'] = null;
                break;
        }

        if (!empty($updates)) {
            $order->update($updates);
        }
    }

    /**
     * Update payment status secara manual
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'payment_status' => 'required|in:pending,settlement,capture,deny,cancel,expire,failure'
            ]);

            $oldPaymentStatus = $order->payment_status;
            $oldOrderStatus = $order->status;

            $updates = ['payment_status' => $request->payment_status];

            // Auto update order status berdasarkan payment status
            switch ($request->payment_status) {
                case 'settlement':
                case 'capture':
                    $updates['status'] = Order::STATUS_PAID;
                    if (!$order->paid_at) {
                        $updates['paid_at'] = now();
                    }
                    break;
                case 'deny':
                case 'cancel':
                case 'expire':
                    $updates['status'] = Order::STATUS_CANCELLED;
                    break;
                case 'failure':
                    $updates['status'] = Order::STATUS_FAILED;
                    break;
                case 'pending':
                    $updates['status'] = Order::STATUS_WAITING_PAYMENT;
                    break;
            }

            $order->update($updates);

            DB::commit();

            Log::info('Payment status updated manually', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $request->payment_status,
                'old_order_status' => $oldOrderStatus,
                'new_order_status' => $order->status,
                'updated_by' => auth()->user()->name ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment status berhasil diupdate',
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'payment_badge_class' => $order->getPaymentStatusBadgeClass(),
                'status_badge_class' => $order->getStatusBadgeClass()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update payment status error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'payment_status' => $request->payment_status,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate payment status. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Sync dengan Midtrans
     */
    public function syncPaymentStatus(Order $order)
    {
        try {
            if (!$order->order_id) {
                return redirect()->back()->with('error', 'Order tidak memiliki Order ID');
            }

            $status = $this->midtransService->getTransactionStatus($order->order_id);
            $statusArray = json_decode(json_encode($status), true);

            if (!isset($statusArray['transaction_status'])) {
                Log::error('Invalid Midtrans response', ['response' => $statusArray]);
                return redirect()->back()->with('error', 'Response dari Midtrans tidak valid');
            }

            $transactionStatus = $statusArray['transaction_status'];
            $fraudStatus = $statusArray['fraud_status'] ?? null;

            $this->updateOrderStatusFromMidtrans($order, $transactionStatus, $fraudStatus, $statusArray);

            return redirect()->back()->with('success', 'Status pembayaran berhasil disinkronisasi');

        } catch (\Exception $e) {
            Log::error('Sync payment status error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal sinkronisasi status: ' . $e->getMessage());
        }
    }

    private function updateOrderStatusFromMidtrans($order, $transactionStatus, $fraudStatus, $fullResponse)
    {
        try {
            DB::beginTransaction();

            $updates = ['midtrans_response' => $fullResponse];

            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        $updates['payment_status'] = 'challenge';
                    } else if ($fraudStatus == 'accept') {
                        $updates['payment_status'] = 'settlement';
                        $updates['status'] = Order::STATUS_PAID;
                        $updates['paid_at'] = now();
                    }
                    break;

                case 'settlement':
                    $updates['payment_status'] = 'settlement';
                    $updates['status'] = Order::STATUS_PAID;
                    $updates['paid_at'] = now();
                    break;

                case 'pending':
                    $updates['payment_status'] = 'pending';
                    $updates['status'] = Order::STATUS_WAITING_PAYMENT;
                    break;

                case 'deny':
                case 'expire':
                case 'cancel':
                    $updates['payment_status'] = $transactionStatus;
                    $updates['status'] = Order::STATUS_CANCELLED;
                    break;

                case 'failure':
                    $updates['payment_status'] = 'failure';
                    $updates['status'] = Order::STATUS_FAILED;
                    break;
            }

            $order->update($updates);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update order status from Midtrans error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'transaction_status' => $transactionStatus
            ]);
            throw $e;
        }
    }

    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Delete order error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus order');
        }
    }

    public function payOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            // Validasi kepemilikan pesanan
            if (auth()->id() !== $order->user_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Hanya boleh bayar ulang jika masih Menunggu Pembayaran
            if ($order->status !== 'Menunggu Pembayaran') {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak bisa dibayar'], 400);
            }

            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Buat order ID baru untuk Midtrans
            $midtransOrderId = $order->order_number . '_' . time();

            // Siapkan item details
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'id' => $item['id'],
                    'price' => (int) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'name' => $item['name'],
                    'category' => 'product'
                ];
            }

            if ($order->shipping_cost > 0) {
                $items[] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                    'category' => 'shipping'
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => (int) $order->total_amount
                ],
                'customer_details' => [
                    'first_name' => $order->customer_first_name,
                    'last_name' => $order->customer_last_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'billing_address' => [
                        'address' => $order->customer_address,
                        'city' => $order->customer_city,
                        'postal_code' => $order->customer_postal_code,
                        'country_code' => 'IDN'
                    ]
                ],
                'item_details' => $items,
                'callbacks' => [
                    'finish' => route('user.orders.index')
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan snap_token & midtrans_order_id ke database
            $order->update([
                'midtrans_order_id' => $midtransOrderId,
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran'
            ], 500);
        }
    }

}