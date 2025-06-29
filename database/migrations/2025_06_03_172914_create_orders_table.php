<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique(); // TEH-timestamp-random
            $table->string('midtrans_order_id')->nullable(); // Order ID yang dikirim ke Midtrans
            $table->string('transaction_id')->nullable(); // Transaction ID dari Midtrans
            $table->json('midtrans_response')->nullable()->after('completed_at');
            $table->unsignedBigInteger('user_id')->nullable();

            // Customer Information
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->string('customer_city');
            $table->string('customer_postal_code');
            $table->string('customer_province');
            $table->text('customer_notes')->nullable();

            // Order Details
            $table->json('items'); // Array of items
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('total_amount', 10, 2);

            // Status
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'completed',
                'cancelled',
                'failed'
            ])->default('pending');

            // Payment Info
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable(); // dari Midtrans
            $table->timestamp('paid_at')->nullable();

            // Tracking
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('midtrans_response');
        });
    }
};