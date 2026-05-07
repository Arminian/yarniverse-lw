<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'options',
        'price',
        'compare_price',
        'stock_quantity',
        'stock_status',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'options'=> 'array',
            'price'=> 'decimal:2',
            'compare_price'=> 'decimal:2',
            'stock_quantity'=> 'integer',
            'is_active'=> 'boolean',
            'sort_order'=> 'integer',
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
    protected function inStock(Builder $query) {
        $query->where('stock_status', 'in_stock')
        ->where('stock_quantity', '>', 0);
    }

    /**
     * Relationships
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function productImages() {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Helpers
     */
    public function getDiscountPercent() {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round(($this->compare_price - $this->price) / ($this->compare_price) * 100);
        } else {
            return 0;
        }
    }

    /**
     * Events
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-'. strtoupper(Str::random(10));
            }
        });
    }
}
