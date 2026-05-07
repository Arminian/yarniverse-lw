<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_order_value',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'value'=> 'decimal:2',
            'minimum_order_value'=> 'decimal:2',
            'maximum_discount'=> 'decimal:2',
            'usage_limit'=> 'integer',
            'usage_limit_per_customer'=> 'integer',
            'starts_at'=> 'datetime',
            'ends_at'=> 'datetime',
            'is_active'=> 'boolean',                
        ];
    }

    /**
     * Scopes
     */
    #[Scope()]
    protected function active(Builder $query) {
        $query->where('is_active', true);
    }

    #[Scope()]
    protected function valid(Builder $query) {
        $now = Carbon::now();
        $query->where('is_active', true) 
        -> where(function($q) use($now){
            $q->where('starts_at', $now) 
            -> orWhere('starts_at', '<=', $now);
        })
        ->where(function($q) use($now){
            $q->where('ends_at', $now)
            ->orWhere('ends_at', '>=', $now);
        });
    }

    /**
     * Relationships
     */
    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function usages() {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Helpers
     */
    public function isValid() {
        if (!$this->is_active) {
            return false;
        }

        elseif ($this->ends_at && $this->starts_at->isFuture()) {
            return false;
        }

        elseif ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        elseif ($this->usage_limit && $this->usage->count() >= $this->usage_limit) {
            return false;
        }

        else return true;
    }

    public function isUsable($customerId) {
        if (!$this->isValid()) {
            return false;
        }

        elseif ($this->usage_limit_per_customer) {
            $usageCount = $this->usages()->where('customer_id', $customerId)->count();
            if ($usageCount >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        else return true;
    }

    public function calculateDiscount($subtotal) {
        if ($this->minimum_order_value && $subtotal < $this->minimum_order_value) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = ($subtotal * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return min($discount, $subtotal);
    }
}
