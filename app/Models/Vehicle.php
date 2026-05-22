<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'first_seen_at',
        'total_visits',
        'notes',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'total_visits' => 'integer',
    ];

    // Relationships
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Helper: increment visit count
    public function incrementVisits()
    {
        $this->increment('total_visits');
    }
}