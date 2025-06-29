<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function syncPaymentStatus(Order $order)
    {
        try {
            if (!$order->order_id) {
                return redirect()->back()->with('error', 'Order tidak memiliki Order ID');
            }

            $status = $this->midtransService->getTransactionStatus($order->order_id);
            
            // ✅ Cara yang aman untuk mengakses property
            $statusArray = json_decode(json_encode($status), true);
            
            // Validasi apakah response valid
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
            'midtrans_response' => $fullResponse,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled,failed'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        // Update timestamps berdasarkan status
        switch ($request->status) {
            case 'shipped':
                $order->update(['shipped_at' => now()]);
                break;
            case 'completed':
                $order->update(['completed_at' => now()]);
                break;
        }

        return redirect()->back()->with('success', 'Status order berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dihapus');
    }
}