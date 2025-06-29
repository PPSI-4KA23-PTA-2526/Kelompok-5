<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Install Midtrans PHP Library terlebih dahulu
// composer require midtrans/midtrans-php
require_once __DIR__ . '/vendor/autoload.php';

// Jika tidak menggunakan composer, download manual dan uncomment baris ini:
// require_once __DIR__ . '/midtrans-php-master/Midtrans.php';

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'SB-Mid-server-NemakLA3BvevwqiknDVh1gB-';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

try {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required data
    if (!isset($data['customer']) || !isset($data['items']) || !isset($data['total'])) {
        throw new Exception('Missing required data');
    }

    // Format items for Midtrans
    $item_details = [];
    foreach ($data['items'] as $item) {
        $item_details[] = [
            'id' => $item['id'],
            'price' => (int) $item['price'],
            'quantity' => (int) $item['quantity'],
            'name' => $item['name']
        ];
    }

    // PERBAIKAN: Selalu tambahkan ongkir sebagai item terpisah
    $shipping_cost = isset($data['shipping']) ? (int) $data['shipping'] : 10000;
    if ($shipping_cost > 0) {
        $item_details[] = [
            'id' => 'shipping',
            'price' => $shipping_cost,
            'quantity' => 1,
            'name' => 'Biaya Pengiriman'
        ];
    }

    // Create unique order ID
    $order_id = 'TEH-' . time() . '-' . rand(1000, 9999);

    // Customer details
    $customer_details = [
        'first_name' => $data['customer']['first_name'],
        'last_name' => $data['customer']['last_name'],
        'email' => $data['customer']['email'],
        'phone' => $data['customer']['phone'],
        'billing_address' => [
            'first_name' => $data['customer']['first_name'],
            'last_name' => $data['customer']['last_name'],
            'address' => $data['customer']['address'],
            'city' => $data['customer']['city'],
            'postal_code' => $data['customer']['postal_code'],
            'phone' => $data['customer']['phone'],
            'country_code' => 'IDN'
        ],
        'shipping_address' => [
            'first_name' => $data['customer']['first_name'],
            'last_name' => $data['customer']['last_name'],
            'address' => $data['customer']['address'],
            'city' => $data['customer']['city'],
            'postal_code' => $data['customer']['postal_code'],
            'phone' => $data['customer']['phone'],
            'country_code' => 'IDN'
        ]
    ];

    // PERBAIKAN: Hitung gross_amount dari item_details (termasuk ongkir)
    $gross_amount = array_reduce($item_details, function ($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    // VALIDASI: Pastikan gross_amount sama dengan total yang dikirim
    $expected_total = (int) $data['total'];
    if ($gross_amount !== $expected_total) {
        throw new Exception("Total amount mismatch. Calculated: {$gross_amount}, Expected: {$expected_total}");
    }

    $transaction_details = [
        'order_id' => $order_id,
        'gross_amount' => $gross_amount
    ];

    // Build the request parameters
    $params = [
        'transaction_details' => $transaction_details,
        'item_details' => $item_details,
        'customer_details' => $customer_details,
        'enabled_payments' => [
            'credit_card',
            'mandiri_clickpay',
            'cimb_clicks',
            'bca_klikbca',
            'bca_klikpay',
            'bri_epay',
            'echannel',
            'permata_va',
            'bca_va',
            'bni_va',
            'other_va',
            'gopay',
            'shopeepay',
            'dana',
            'ovo',
            'qris',
            'akulaku'
        ],
        'vtweb' => []
    ];

    // Get Snap Payment Page URL
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // Return success response
    echo json_encode([
        'success' => true,
        'snap_token' => $snapToken,
        'order_id' => $order_id,
        'debug' => [
            'gross_amount' => $gross_amount,
            'item_count' => count($item_details),
            'items' => $item_details
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}