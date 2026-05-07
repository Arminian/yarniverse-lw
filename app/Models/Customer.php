<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'is_active',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at'=> 'datetime',
            'password'=> 'hashed',
            'date_of_birth'=> 'date',
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

    /**
     * Relationships
     */
    public function addresses() {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress() {
        return $this->belongsTo(Address::class)->where('is_default', true);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function couponUsages() {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Helpers
     */
    public function getTotalSpending() {
        return $this->orders()->where('payment_status', 'paid')->sum('total');
    }
    public function getOrdersCount() {
        return $this->orders()->count();
    }
}
