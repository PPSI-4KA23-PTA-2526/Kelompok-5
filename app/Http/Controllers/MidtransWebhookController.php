<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
        
        // Tidak perlu middleware di sini karena sudah di-handle di route
    }

    public function handle(Request $request)
    {
        // Log request untuk debugging
        Log::info('✅ Midtrans Webhook Received:', [
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        try {
            $notification = $request->all();

            // Validasi required fields
            if (!isset($notification['order_id']) || !isset($notification['transaction_status'])) {
                Log::error('Invalid webhook data: missing required fields');
                return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            // Validasi signature (opsional tapi recommended)
            if (!$this->validateSignature($notification)) {
                Log::error('Invalid signature for order_id: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            }

            // Cari order berdasarkan order_id
            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                // Debug: tampilkan semua order yang ada
                $allOrderIds = Order::pluck('order_id')->toArray();
                Log::error('Order not found for order_id: ' . $orderId, [
                    'available_orders' => $allOrderIds,
                    'total_orders' => count($allOrderIds)
                ]);
                
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Order not found',
                    'order_id_searched' => $orderId,
                    'available_orders' => $allOrderIds
                ], 404);
            }

            // Update informasi transaksi
            $order->update([
                'transaction_id' => $notification['transaction_id'] ?? null,
                'payment_method' => $notification['payment_type'] ?? null,
                'midtrans_response' => $notification,
            ]);

            // Update status berdasarkan notification
            $this->updateOrderStatus($order, $transactionStatus, $fraudStatus);

            Log::info('✅ Order updated successfully for order_id: ' . $orderId . ' | Status: ' . $transactionStatus);

            return response()->json(['status' => 'success', 'message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('❌ Midtrans Webhook Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function validateSignature($notification)
    {
        // Skip validation jika tidak ada signature key
        if (!isset($notification['signature_key'])) {
            return true; // Untuk testing
        }

        $serverKey = config('services.midtrans.server_key');
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        return $signatureKey === $notification['signature_key'];
    }

    private function updateOrderStatus($order, $transactionStatus, $fraudStatus)
    {
        $paymentStatus = $transactionStatus;
        $orderStatus = $order->status;

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus == 'challenge') {
                    $paymentStatus = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $paymentStatus = 'settlement';
                    $orderStatus = 'paid';
                    $order->paid_at = now();
                }
                break;

            case 'settlement':
                $paymentStatus = 'settlement';
                $orderStatus = 'paid';
                $order->paid_at = now();
                break;

            case 'pending':
                $paymentStatus = 'pending';
                $orderStatus = 'pending';
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                $paymentStatus = $transactionStatus;
                $orderStatus = 'cancelled';
                break;

            case 'failure':
                $paymentStatus = 'failure';
                $orderStatus = 'failed';
                break;
        }

        $order->update([
            'payment_status' => $paymentStatus,
            'status' => $orderStatus,
            'paid_at' => $order->paid_at,
        ]);

        Log::info('Order status updated:', [
            'order_id' => $order->order_id,
            'old_status' => $order->getOriginal('status'),
            'new_status' => $orderStatus,
            'payment_status' => $paymentStatus
        ]);
    }
}