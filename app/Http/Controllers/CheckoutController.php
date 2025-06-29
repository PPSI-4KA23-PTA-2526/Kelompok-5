<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function processPayment(Request $request): JsonResponse
    {
        try {
            // Konfigurasi Midtrans
            Config::$serverKey = config('services.midtrans.server_key', 'SB-Mid-server-NemakLA3BvevwqiknDVh1gB-');
            Config::$isProduction = config('services.midtrans.is_production', false);
            Config::$isSanitized = config('services.midtrans.is_sanitized', true);
            Config::$is3ds = config('services.midtrans.is_3ds', true);

            // Log request untuk debugging
            Log::info('💰 Checkout Request Received:', [
                'request_data' => $request->all(),
                'user_id' => auth()->check() ? auth()->id() : 'guest'
            ]);

            // Validasi request dengan error handling yang lebih baik
            $validated = $request->validate([
                'customer.first_name' => 'required|string|max:255',
                'customer.last_name' => 'required|string|max:255',
                'customer.email' => 'required|email|max:255',
                'customer.phone' => 'required|string|max:20',
                'customer.address' => 'required|string|max:500',
                'customer.city' => 'required|string|max:100',
                'customer.province' => 'required|string|max:100',
                'customer.postal_code' => 'required|string|max:10',
                'notes' => 'nullable|string|max:500',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'subtotal' => 'required|numeric|min:0',
                'shipping_cost' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0'
            ]);

            // Generate unique order_id
            $orderId = 'TEH-' . time() . '-' . rand(1000, 9999);

            // Gunakan database transaction untuk memastikan konsistensi
            DB::beginTransaction();

            try {
                // Simpan order ke database
                $order = Order::create([
                    'order_id' => $orderId,
                    'order_number' => $orderId,
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'customer_first_name' => $validated['customer']['first_name'],
                    'customer_last_name' => $validated['customer']['last_name'],
                    'customer_email' => $validated['customer']['email'],
                    'customer_phone' => $validated['customer']['phone'],
                    'customer_address' => $validated['customer']['address'],
                    'customer_city' => $validated['customer']['city'],
                    'customer_province' => $validated['customer']['province'],
                    'customer_postal_code' => $validated['customer']['postal_code'],
                    'customer_notes' => $validated['notes'] ?? null,
                    'shipping_address' => $validated['customer']['address'],
                    'items' => $validated['items'],
                    'subtotal' => $validated['subtotal'],
                    'shipping_cost' => $validated['shipping_cost'],
                    'total_amount' => $validated['total'],
                    'status' => Order::STATUS_PENDING,
                    'payment_status' => Order::PAYMENT_PENDING
                ]);

                Log::info('✅ Order created successfully:', [
                    'order_id' => $orderId,
                    'total_amount' => $validated['total']
                ]);

                // Format item details untuk Midtrans
                $itemDetails = [];
                foreach ($validated['items'] as $item) {
                    $itemDetails[] = [
                        'id' => $item['id'],
                        'price' => (int) $item['price'],
                        'quantity' => (int) $item['quantity'],
                        'name' => $item['name'],
                        'category' => 'product'
                    ];
                }

                // Tambahkan ongkir sebagai item
                if ($validated['shipping_cost'] > 0) {
                    $itemDetails[] = [
                        'id' => 'SHIPPING',
                        'price' => (int) $validated['shipping_cost'],
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim',
                        'category' => 'shipping'
                    ];
                }

                // Buat parameter untuk Midtrans
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $validated['total']
                    ],
                    'customer_details' => [
                        'first_name' => $validated['customer']['first_name'],
                        'last_name' => $validated['customer']['last_name'],
                        'email' => $validated['customer']['email'],
                        'phone' => $validated['customer']['phone'],
                        'billing_address' => [
                            'first_name' => $validated['customer']['first_name'],
                            'last_name' => $validated['customer']['last_name'],
                            'address' => $validated['customer']['address'],
                            'city' => $validated['customer']['city'],
                            'postal_code' => $validated['customer']['postal_code'],
                            'country_code' => 'IDN'
                        ],
                        'shipping_address' => [
                            'first_name' => $validated['customer']['first_name'],
                            'last_name' => $validated['customer']['last_name'],
                            'address' => $validated['customer']['address'],
                            'city' => $validated['customer']['city'],
                            'postal_code' => $validated['customer']['postal_code'],
                            'country_code' => 'IDN'
                        ]
                    ],
                    'item_details' => $itemDetails
                ];

                Log::info('🔄 Creating Midtrans Snap Token with params:', $params);

                // Buat Snap Token dari Midtrans
                $snapToken = Snap::getSnapToken($params);

                // Update order dengan midtrans info
                $order->update([
                    'midtrans_order_id' => $orderId
                ]);

                DB::commit();

                Log::info('✅ Snap token created successfully:', [
                    'order_id' => $orderId,
                    'snap_token' => substr($snapToken, 0, 20) . '...' // Log partial token for security
                ]);

                // Return response dengan format yang konsisten
                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'order_id' => $orderId,
                    'message' => 'Order created successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation Error:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Data tidak valid',
                'validation_errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Checkout Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}