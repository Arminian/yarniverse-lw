<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\isNumeric;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Scopes
     */
    #[Scope()]
    protected function group(Builder $query, string $group)
    {
        $query->where('group', $group);
    }

    /**
     * Helpers
     */
    public static function get(string $key, $default = null)
    {
        $settings = static::where('key', $key)->first();

        if (!$settings) {
            return $default;
        } else {
            return static::castValue($settings->value, $settings->type);
        }
    }

    public function set(string $key, $value, $type = 'string', $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
    }

    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => isNumeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true),
        };
    }
}
