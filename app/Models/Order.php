<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'coupon_id',

        'subtotal',
        'discount_amount',
        'shipping_cost',
        'tax_amount',
        'total',

        'shipping_full_name',
        'shipping_phone',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_postal_code',
        'shipping_address_line_one',
        'shipping_address_line_two',
        
        'payment_method',
        'payment_status',
        'transaction_id',
        'status',
        'tracking_number',
        'customer_notes',
        'admin_notes',
    ];

    /**
     * Scopes
     */    
    #[Scope()]
    protected function ofStatus(Builder $query, string $status) {
        $query->where('status', $status);
    }

    #[Scope()]
    protected function paymentStatus(Builder $query, string $status) {
        $query->where('payment_status', $status);
    }

     #[Scope()]   
    protected function pending(Builder $query) {
        $query->where('status', 'pending');
    }

    #[Scope()]
    protected function processing(Builder $query) {
        $query->where('status','processing');
    }

    #[Scope()]
    protected function shipped(Builder $query) {
        $query->where('status','shipped');
    }

    #[Scope()]
    protected function delivered(Builder $query) {
        $query->where('status','delivered');
    }

    /**
     * Relationships
     */
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function coupon() {
        return $this->belongsTo(Coupon::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories() {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Helpers
     */
    public function getShippingAddress() {
        return implode(', ', array_filter([
            $this->shipping_address_line_one,
            $this->shipping_address_line_two,
            $this->country,
            $this->state,
            $this->city,
            $this->postal_code,
        ]));
    }

    public function updateStatus($status, $notes=null, $userId=null) {
        $this->update(['status'=> $status]);

        $this->statusHistories()->create([
            'status'=> $status,
            'notes'=> $notes,
            'user_id'=> $userId
        ]);
    }

    /**
     * Events
     */
    protected static function boot() {
        parent::boot();

        static::creating(function($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-'. strtoupper(uniqid());
            }
        });

        static::created(function($order) {
            $order->statusHistories()->create([
                'status'=> $order->status,
                'notes'=> 'Order has been created',
            ]);

        });
    }
}
