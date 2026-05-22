<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'must_change_password',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function issuedTickets()
    {
        return $this->hasMany(Ticket::class, 'issued_by');
    }

    public function exitedTickets()
    {
        return $this->hasMany(Ticket::class, 'exited_by');
    }

    public function processedTransactions()
    {
        return $this->hasMany(Transaction::class, 'processed_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function updatedSettings()
    {
        return $this->hasMany(Setting::class, 'updated_by');
    }

    public function createdReportSchedules()
    {
        return $this->hasMany(ReportSchedule::class, 'created_by');
    }

    public function slotConversions()
    {
        return $this->hasMany(SlotTypeConversionLog::class, 'admin_id');
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSecurity()
    {
        return $this->role === 'security';
    }

    public function needsPasswordChange(): bool
    {
        return $this->must_change_password === true;
    }
}