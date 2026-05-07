<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'is_primary'=> 'boolean',
            'sort_order'=> 'integer',
        ];
    }

    /**
     * Scopes
     */

    #[Scope()]
    protected function primary(Builder $query) {
        $query->where('is_primary', true);
    }

    /**
     * Relationships
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function productVariant() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Helpers
     */
    public function getImageUrl() {
        return asset('storage/'. $this->image_path);
    }
}
