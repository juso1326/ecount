<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get setting value with type casting
     */
    public function getValue()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->value) ? (int)$this->value : 0,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): bool
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return false;
        }

        // Convert value based on type
        if ($setting->type === 'json' && is_array($value)) {
            $value = json_encode($value);
        } elseif ($setting->type === 'boolean') {
            $value = $value ? 'true' : 'false';
        }

        $setting->value = $value;
        return $setting->save();
    }
}
