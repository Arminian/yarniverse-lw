<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'full_name',
        'phone',
        'country',
        'state',
        'city',
        'postal_code',
        'address_line_one',
        'address_line_two',
        'is_default',
        'type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'is_default'=> 'boolean',
        ];
    }

    /**
     * Scopes
     */
    #[Scope()]
    protected function default(Builder $query) {
        $query->where('is_default','true');
    }

    #[Scope()]
    protected function ofType(Builder $query, string $type) {
        $query->where('type',$type);
    }

    /**
     * Relationships
     */
    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Summary of getFullAddress
     * @return string
     */
    public function getFullAddress() {
        return implode(', ', array_filter([
            $this->address_line_one,
            $this->address_line_two,
            $this->country,
            $this->state,
            $this->city,
            $this->postal_code,
        ]));
    }
}
