<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'ticket_id',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Helper: log an action
    public static function log($userId, $action, $ticketId = null, $details = null, $ip = null)
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'ticket_id' => $ticketId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $ip ?? request()->ip(),
        ]);
    }
}