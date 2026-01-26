<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_id',
        'order_number',
        'midtrans_order_id',
        'transaction_id',
        'payment_method',
        'payment_status',
        'paid_at',
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'notes',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_postal_code',
        'customer_province',
        'customer_notes',
        'items',
        'subtotal',
        'shipping_cost',
        'tracking_number',
        'shipped_at',
        'completed_at',
        'midtrans_response',
        'payment_proof',
        'payment_proof_uploaded_at',
    ];

    protected $casts = [
        'items' => 'array',
        'midtrans_response' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'payment_proof_uploaded_at' => 'datetime',
    ];

    // Status constants - menggunakan bahasa Indonesia sesuai database
    const STATUS_WAITING_PAYMENT = 'Menunggu Pembayaran';
    const STATUS_PAID = 'Dibayar';
    const STATUS_PROCESSING = 'Diproses';
    const STATUS_SHIPPED = 'Dikirim';
    const STATUS_DELIVERED = 'Selesai';
    const STATUS_CANCELLED = 'Dibatalkan';
    const STATUS_REFUNDED = 'Dikembalikan';
    const STATUS_FAILED = 'Gagal';

    // Mapping untuk backward compatibility dengan controller
    const STATUS_MAPPING = [
        'waiting_payment' => 'Menunggu Pembayaran',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Selesai',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'refunded' => 'Dikembalikan',
        'failed' => 'Gagal',
        'pending' => 'Menunggu Pembayaran'
    ];

    // Reverse mapping untuk API response
    const REVERSE_STATUS_MAPPING = [
        'Menunggu Pembayaran' => 'waiting_payment',
        'Dibayar' => 'paid',
        'Diproses' => 'processing',
        'Dikirim' => 'shipped',
        'Selesai' => 'delivered',
        'Dibatalkan' => 'cancelled',
        'Dikembalikan' => 'refunded',
        'Gagal' => 'failed'
    ];

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_SETTLEMENT = 'settlement';
    const PAYMENT_CAPTURE = 'capture';
    const PAYMENT_DENY = 'deny';
    const PAYMENT_CANCEL = 'cancel';
    const PAYMENT_EXPIRE = 'expire';
    const PAYMENT_FAILURE = 'failure';

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     * PERBAIKAN: Menggunakan 'id' sebagai foreign key yang benar
     */
    public function orderItems()
    {
        // Periksa apakah order_items table menggunakan 'order_id' yang merujuk ke 'id' atau 'order_id'
        // Berdasarkan migration, foreign key adalah 'order_id' yang merujuk ke 'id'
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Alternative: Jika order_items.order_id merujuk ke orders.order_id (bukan orders.id)
     * Uncomment baris di bawah dan comment yang di atas jika menggunakan orders.order_id
     */
    // public function orderItems()
    // {
    //     return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    // }

    /**
     * Set status dengan mapping otomatis
     */
    public function setStatusAttribute($value)
    {
        // Jika value adalah English status, convert ke Indonesian
        if (isset(self::STATUS_MAPPING[$value])) {
            $this->attributes['status'] = self::STATUS_MAPPING[$value];
        } else {
            $this->attributes['status'] = $value;
        }
    }

    /**
     * Get status untuk API response (English format)
     */
    public function getApiStatusAttribute()
    {
        return self::REVERSE_STATUS_MAPPING[$this->status] ?? $this->status;
    }

    /**
     * Get payment status badge class for UI.
     */
    public function getPaymentStatusBadgeClass()
    {
        return match($this->payment_status) {
            'settlement', 'capture' => 'bg-success',
            'pending' => 'bg-warning',
            'deny', 'cancel', 'expire', 'failure' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get order status badge class for UI.
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'Selesai' => 'bg-success',
            'Dibayar' => 'bg-info',
            'Diproses' => 'bg-primary',
            'Dikirim' => 'bg-purple',
            'Menunggu Pembayaran' => 'bg-warning',
            'Dibatalkan' => 'bg-danger',
            'Dikembalikan' => 'bg-secondary',
            'Gagal' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status label (sudah dalam bahasa Indonesia)
     */
    public function getStatusLabel()
    {
        return $this->status;
    }

    /**
     * Get customer full name attribute.
     */
    public function getCustomerFullNameAttribute()
    {
        if ($this->customer_first_name && $this->customer_last_name) {
            return trim($this->customer_first_name . ' ' . $this->customer_last_name);
        }
        
        if ($this->user) {
            return $this->user->name;
        }
        
        return 'N/A';
    }

    /**
     * Scope for filtering by order status (accepts both English and Indonesian)
     */
    public function scopeByStatus(Builder $query, string $status)
    {
        $dbStatus = self::STATUS_MAPPING[$status] ?? $status;
        return $query->where('status', $dbStatus);
    }

    /**
     * Scope for filtering by payment status.
     */
    public function scopeByPaymentStatus(Builder $query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope for recent orders.
     */
    public function scopeRecent(Builder $query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['settlement', 'capture']) || 
               in_array($this->status, ['Dibayar', 'Diproses', 'Dikirim', 'Selesai']);
    }

    /**
     * Check if order is completed/delivered.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Selesai';
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['Dikirim', 'Selesai', 'Dibatalkan', 'Dikembalikan']);
    }

    /**
     * Check if order is waiting for payment.
     */
    public function isWaitingPayment(): bool
    {
        return $this->status === 'Menunggu Pembayaran';
    }

    /**
     * Get all available statuses for dropdown/filter.
     */
    public static function getAvailableStatuses(): array
    {
        return [
            'waiting_payment' => 'Menunggu Pembayaran',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Dikembalikan',
        ];
    }

    /**
     * Get available statuses in database format
     */
    public static function getDbStatuses(): array
    {
        return [
            'Menunggu Pembayaran',
            'Dibayar',
            'Diproses',
            'Dikirim',
            'Selesai',
            'Dibatalkan',
            'Dikembalikan',
            'Gagal'
        ];
    }
}