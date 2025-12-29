<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'shipping_cost',
        'grand_total',
        'status',
        'payment_status',
        'payment_type',
        'payment_method',
        'payment_proof',
        'shipping_method_id',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'shipping_address',
        'shipping_phone',
        'shipping_latitude',
        'shipping_longitude',
        'phone',
        'notes',
        'admin_notes',
        'snap_token',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_latitude' => 'decimal:8',
        'shipping_longitude' => 'decimal:8',
    ];

    /**
     * Boot method to auto-generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the user who owns the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Alias for orderItems to match frontend expectations
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the shipping method
     */
    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid orders
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
