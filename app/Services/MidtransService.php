<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function createTransaction($order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id, // Menggunakan order_id dari database Anda
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_first_name,
                'last_name' => $order->customer_last_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'billing_address' => [
                    'first_name' => $order->customer_first_name,
                    'last_name' => $order->customer_last_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'address' => $order->customer_address,
                    'city' => $order->customer_city,
                    'postal_code' => $order->customer_postal_code,
                    'country_code' => 'IDN'
                ],
                'shipping_address' => [
                    'first_name' => $order->customer_first_name,
                    'last_name' => $order->customer_last_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'address' => $order->customer_address,
                    'city' => $order->customer_city,
                    'postal_code' => $order->customer_postal_code,
                    'country_code' => 'IDN'
                ]
            ],
            'item_details' => $this->formatItemDetails($order->items),
        ];

        // Tambahkan shipping cost jika ada
        if ($order->shipping_cost > 0) {
            $params['item_details'][] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
                'category' => 'shipping'
            ];
        }

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Update order dengan midtrans_order_id
            $order->update([
                'midtrans_order_id' => $order->order_id
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Error creating Midtrans transaction: ' . $e->getMessage());
        }
    }

    private function formatItemDetails($items)
    {
        $itemDetails = [];
        
        foreach ($items as $item) {
            $itemDetails[] = [
                'id' => $item['id'] ?? $item['product_id'],
                'price' => (int) $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'] ?? $item['product_name'],
                'category' => $item['category'] ?? 'product'
            ];
        }
        
        return $itemDetails;
    }

    public function getTransactionStatus($orderId)
    {
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            throw new \Exception('Error getting transaction status: ' . $e->getMessage());
        }
    }
}