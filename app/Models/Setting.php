<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'updated_by',
    ];

    // Relationships
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper: get setting value by key
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // Helper: set setting value
    public static function set($key, $value, $updatedBy = null)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'updated_by' => $updatedBy]
        );
    }
}