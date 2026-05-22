<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slot_number',
        'type',
        'status',
        'is_active',
        'zone_id',
        'zone_name'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function currentTicket()
    {
        return $this->hasOne(Ticket::class)->where('status', 'active');
    }

    // Helpers
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function isWheelchair()
    {
        return $this->type === 'wheelchair';
    }

    public function isDelivery()
    {
        return $this->type === 'delivery';
    }

    // Relationship
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }    
}