<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    /**
     * Scopes
     */
    #[Scope()]
    protected function active(Builder $query)
    {
        $query->where('is_active', true);
    }

    #[Scope()]
    protected function sorted(Builder $query)
    {
        $query->orderBy('sort_order', 'asc');
    }

    /**
     * Relationships
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->empty)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
