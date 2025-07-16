<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        // Pertama, update semua data yang ada ke format baru
        DB::statement("UPDATE orders SET status = 'Menunggu Pembayaran' WHERE status IN ('pending', 'waiting_payment')");
        DB::statement("UPDATE orders SET status = 'Dibayar' WHERE status = 'paid'");
        DB::statement("UPDATE orders SET status = 'Diproses' WHERE status = 'processing'");
        DB::statement("UPDATE orders SET status = 'Dikirim' WHERE status = 'shipped'");
        DB::statement("UPDATE orders SET status = 'Selesai' WHERE status IN ('completed', 'delivered')");
        DB::statement("UPDATE orders SET status = 'Dibatalkan' WHERE status = 'cancelled'");
        DB::statement("UPDATE orders SET status = 'Dikembalikan' WHERE status = 'refunded'");
        DB::statement("UPDATE orders SET status = 'Gagal' WHERE status = 'failed'");

        // Kemudian update enum definition
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('Menunggu Pembayaran','Dibayar','Diproses','Dikirim','Selesai','Dibatalkan','Dikembalikan','Gagal') NOT NULL DEFAULT 'Menunggu Pembayaran'");
    }

    public function down(): void
    {
        // Rollback ke format lama
        DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'Menunggu Pembayaran'");
        DB::statement("UPDATE orders SET status = 'paid' WHERE status = 'Dibayar'");
        DB::statement("UPDATE orders SET status = 'processing' WHERE status = 'Diproses'");
        DB::statement("UPDATE orders SET status = 'shipped' WHERE status = 'Dikirim'");
        DB::statement("UPDATE orders SET status = 'completed' WHERE status = 'Selesai'");
        DB::statement("UPDATE orders SET status = 'cancelled' WHERE status = 'Dibatalkan'");
        DB::statement("UPDATE orders SET status = 'refunded' WHERE status = 'Dikembalikan'");
        DB::statement("UPDATE orders SET status = 'failed' WHERE status = 'Gagal'");

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','paid','processing','shipped','completed','cancelled','failed') NOT NULL DEFAULT 'pending'");
    }
};