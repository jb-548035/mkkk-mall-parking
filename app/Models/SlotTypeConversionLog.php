<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotTypeConversionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'conversion_type',
        'affected_slot_ids',
        'reason',
        'reverted_at',
        'reverted_by',
    ];

    protected $casts = [
        'affected_slot_ids' => 'array',
        'reverted_at' => 'datetime',
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function revertedBy()
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }
}