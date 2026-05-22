<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'qr_code',
        'vehicle_id',
        'plate_number',
        'entry_time',
        'exit_time',
        'fee',
        'rate_at_entry',
        'grace_period_at_entry',
        'is_delivery',
        'status',
        'void_reason',
        'parking_slot_id',
        'issued_by',
        'exited_by',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'fee' => 'decimal:2',
        'rate_at_entry' => 'decimal:2',
        'grace_period_at_entry' => 'integer',
        'is_delivery' => 'boolean',
    ];

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function parkingSlot()
    {
        return $this->belongsTo(ParkingSlot::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function exitedBy()
    {
        return $this->belongsTo(User::class, 'exited_by');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Helpers
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPaid()
    {
        return $this->transaction()->exists();
    }

    public function calculateFee()
    {
        if ($this->is_delivery) {
            return 0.00;
        }

        if (!$this->exit_time) {
            return 0.00;
        }

        $minutesParked = $this->exit_time->diffInMinutes($this->entry_time);
        $billableMinutes = max(0, $minutesParked - $this->grace_period_at_entry);
        $hours = ceil($billableMinutes / 60);

        return $hours * $this->rate_at_entry;
    }
}