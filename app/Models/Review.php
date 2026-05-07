<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_purchase_verified',
        'is_approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'rating'=> 'integer',
            'is_purchase_verified'=> 'boolean',
            'is_approved'=> 'boolean',
        ];
    }

    /**
     * Scopes
     */
    #[Scope()]
    protected function approved(Builder $query) {
        $query->where('is_approved', true);
    }

    #[Scope()]
    protected function revified(Builder $query) {
        $query->where('is_purchase_verified', true);
    }

    #[Scope()]
    protected function rating(Builder $query, int $rating) {
        $query->where('rating', $rating);
    }

    /**
     * Relationships
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
