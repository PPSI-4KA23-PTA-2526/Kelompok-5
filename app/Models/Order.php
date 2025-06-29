<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
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
        'midtrans_response'
    ];

    protected $casts = [
        'items' => 'array',
        'midtrans_response' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_SETTLEMENT = 'settlement';
    const PAYMENT_CAPTURE = 'capture';
    const PAYMENT_DENY = 'deny';
    const PAYMENT_CANCEL = 'cancel';
    const PAYMENT_EXPIRE = 'expire';
    const PAYMENT_FAILURE = 'failure';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPaymentStatusBadgeClass()
    {
        return match($this->payment_status) {
            'settlement', 'capture' => 'bg-success',
            'pending' => 'bg-warning',
            'deny', 'cancel', 'expire', 'failure' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'completed' => 'bg-success',
            'paid', 'processing' => 'bg-info',
            'shipped' => 'bg-primary',
            'pending' => 'bg-warning',
            'cancelled', 'failed' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getCustomerFullNameAttribute()
    {
        return $this->customer_first_name . ' ' . $this->customer_last_name;
    }

    /**
     * Scope for filtering by status.
     *
     * Usage: Order::byStatus('pending')->get();
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
