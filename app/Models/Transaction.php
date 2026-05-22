<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'amount',
        'payment_method',
        'gateway_reference',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Helpers
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    public function isDeliveryOverride()
    {
        return $this->payment_method === 'delivery_override';
    }

    // Add a helper method
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'e_wallet' => 'E-Wallet',
            'delivery_override' => 'Delivery (Free)',
        ];
        return $labels[$this->payment_method] ?? ucfirst($this->payment_method);
    }    
}