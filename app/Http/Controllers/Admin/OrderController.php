<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
     * ========================================
     * M-BANKING PAYMENT VERIFICATION METHODS
     * ========================================
     */

    /**
     * Verifikasi pembayaran M-Banking
     */
    public function verifyPayment(Order $order)
    {
        try {
            DB::beginTransaction();

            // Validasi bahwa order menggunakan M-Banking
            if ($order->payment_method !== 'mbanking') {
                return redirect()->back()->with('error', 'Order ini bukan menggunakan M-Banking');
            }

            // Validasi ada bukti pembayaran
            if (!$order->payment_proof) {
                return redirect()->back()->with('error', 'Belum ada bukti pembayaran yang diupload');
            }

            // Update status pembayaran dan order
            $order->update([
                'payment_status' => 'settlement',
                'status' => Order::STATUS_PAID, // "Dibayar"
                'paid_at' => now()
            ]);

            DB::commit();

            Log::info('M-Banking payment verified', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'verified_by' => auth()->user()->name ?? 'admin',
                'verified_at' => now()
            ]);

            return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi! Status order telah diupdate menjadi "Dibayar".');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error verifying M-Banking payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pembayaran M-Banking
     */
    public function rejectPayment(Order $order)
    {
        try {
            DB::beginTransaction();

            // Validasi bahwa order menggunakan M-Banking
            if ($order->payment_method !== 'mbanking') {
                return redirect()->back()->with('error', 'Order ini bukan menggunakan M-Banking');
            }

            // Update status pembayaran
            $order->update([
                'payment_status' => 'deny',
                'status' => Order::STATUS_CANCELLED, // "Dibatalkan"
            ]);

            DB::commit();

            Log::info('M-Banking payment rejected', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'rejected_by' => auth()->user()->name ?? 'admin',
                'rejected_at' => now()
            ]);

            return redirect()->back()->with('success', 'Pembayaran ditolak. Status order telah diupdate menjadi "Dibatalkan".');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error rejecting M-Banking payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Delete payment proof (opsional - jika admin ingin hapus bukti yang salah)
     */
    public function deletePaymentProof(Order $order)
    {
        try {
            DB::beginTransaction();

            if (!$order->payment_proof) {
                return redirect()->back()->with('error', 'Tidak ada bukti pembayaran untuk dihapus');
            }

            // Hapus file dari storage
            if (Storage::disk('public')->exists($order->payment_proof)) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            // Update order
            $order->update([
                'payment_proof' => null,
                'payment_proof_uploaded_at' => null,
                'payment_status' => 'pending'
            ]);

            DB::commit();

            Log::info('Payment proof deleted', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'deleted_by' => auth()->user()->name ?? 'admin'
            ]);

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting payment proof', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Gagal menghapus bukti pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * EXISTING METHODS (UPDATED)
     * ========================================
     */

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
     * Update payment status secara manual (dengan redirect untuk verifikasi bukti transfer)
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

            // Redirect ke halaman detail order dengan success message
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Pembayaran berhasil diverifikasi! Status order telah diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update payment status error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'payment_status' => $request->payment_status ?? null,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
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

    /**
     * Update order (untuk tracking number, dll)
     */
    public function update(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();
            
            $request->validate([
                'tracking_number' => 'nullable|string|max:100',
            ]);
            
            $updates = [];
            
            if ($request->filled('tracking_number')) {
                $updates['tracking_number'] = $request->tracking_number;
                
                // Auto update status ke "Dikirim" jika tracking number ditambahkan
                if ($order->status === 'Diproses' || $order->status === 'Dibayar') {
                    $updates['status'] = Order::STATUS_SHIPPED;
                    if (!$order->shipped_at) {
                        $updates['shipped_at'] = now();
                    }
                }
            }
            
            if (!empty($updates)) {
                $order->update($updates);
            }
            
            DB::commit();
            
            Log::info('Order updated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'updates' => $updates,
                'updated_by' => auth()->user()->name ?? 'system'
            ]);
            
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Data order berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update order error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui order: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            // Hapus bukti pembayaran jika ada
            if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            $order->delete();
            
            Log::info('Order deleted', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'deleted_by' => auth()->user()->name ?? 'admin'
            ]);

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
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran'
            ], 500);
        }
    }
}