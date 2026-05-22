<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'total_slots', 'pwd_slots', 
        'delivery_slots', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_slots' => 'integer',
        'pwd_slots' => 'integer',
        'delivery_slots' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function parkingSlots()
    {
        return $this->hasMany(ParkingSlot::class);
    }

    public function standardSlots()
    {
        return $this->hasMany(ParkingSlot::class)->where('type', 'standard');
    }

    public function pwdSlots()
    {
        return $this->hasMany(ParkingSlot::class)->where('type', 'wheelchair');
    }

    public function deliverySlots()
    {
        return $this->hasMany(ParkingSlot::class)->where('type', 'delivery');
    }

    // Helpers
    public function getAvailableSlotsCount()
    {
        return $this->parkingSlots()->where('status', 'available')->count();
    }

    public function getOccupiedSlotsCount()
    {
        return $this->parkingSlots()->where('status', 'occupied')->count();
    }

    public function getOccupancyRate()
    {
        $total = $this->total_slots;
        if ($total == 0) return 0;
        return round(($this->getOccupiedSlotsCount() / $total) * 100, 1);
    }
}