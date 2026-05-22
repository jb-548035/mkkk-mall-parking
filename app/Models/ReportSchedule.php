<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'frequency',
        'recipient_email',
        'report_type',
        'is_active',
        'last_sent_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}