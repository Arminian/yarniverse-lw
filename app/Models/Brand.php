<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'is_active',
        'sort_order',
    ];

    /**
     * Scopes
     */
    #[Scope()]
    protected function active(Builder $query) {
        $query->where('is_active', true);
    }

    #[Scope()]
    protected function sorted(Builder $query) {
        $query->orderBy('sort_order', 'asc');
    }

    /**
     * Relationships
     */
    public function products() {
        return $this->hasMany(Product::class);
    }

    /**
     * Events
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {  
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && empty($brand->empty)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }
}
