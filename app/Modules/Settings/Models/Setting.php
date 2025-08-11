<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'options',
        'is_public',
        'is_autoload',
        'order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'is_autoload' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get the value attribute based on type.
     */
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
            case 'array':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Set the value attribute based on type.
     */
    public function setValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                $this->attributes['value'] = $value ? '1' : '0';
                break;
            case 'json':
            case 'array':
                $this->attributes['value'] = json_encode($value);
                break;
            default:
                $this->attributes['value'] = $value;
        }
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include autoload settings.
     */
    public function scopeAutoload($query)
    {
        return $query->where('is_autoload', true);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, array $attributes = []): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            array_merge(['value' => $value], $attributes)
        );

        // Clear cache
        Cache::forget("setting_{$key}");
        Cache::forget('settings_all');
        Cache::forget('settings_autoload');
        Cache::forget("settings_group_{$setting->group}");

        return $setting;
    }

    /**
     * Get all settings.
     */
    public static function all($columns = ['*'])
    {
        return Cache::remember('settings_all', 3600, function () use ($columns) {
            return parent::all($columns);
        });
    }

    /**
     * Get all autoload settings.
     */
    public static function getAllAutoload()
    {
        return Cache::remember('settings_autoload', 3600, function () {
            return self::autoload()->orderBy('order')->get();
        });
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group)
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return self::group($group)->orderBy('order')->get();
        });
    }

    /**
     * Get settings as key-value pairs.
     */
    public static function getAsKeyValue(string $group = null)
    {
        $query = self::query();
        
        if ($group) {
            $query->group($group);
        }

        return $query->pluck('value', 'key')->toArray();
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('settings_all');
        Cache::forget('settings_autoload');
        
        // Clear individual setting caches
        self::all()->each(function ($setting) {
            Cache::forget("setting_{$setting->key}");
        });
        
        // Clear group caches
        self::distinct('group')->pluck('group')->each(function ($group) {
            Cache::forget("settings_group_{$group}");
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function ($setting) {
            self::clearCache();
        });

        static::deleted(function ($setting) {
            self::clearCache();
        });
    }
}
