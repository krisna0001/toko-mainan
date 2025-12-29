<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected $appends = ['product_name'];

    /**
     * Boot method to auto-calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            $orderItem->subtotal = $orderItem->price * $orderItem->quantity;
        });

        static::updating(function ($orderItem) {
            if ($orderItem->isDirty(['price', 'quantity'])) {
                $orderItem->subtotal = $orderItem->price * $orderItem->quantity;
            }
        });
    }

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor for product name
     */
    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : 'Unknown Product';
    }
}
