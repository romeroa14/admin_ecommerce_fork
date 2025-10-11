<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_details',
        'gateway_response',
        'error_message',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    // Scope para pagos exitosos
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope para pagos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para pagos fallidos
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Scope por método de pago
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Accessor para verificar si el pago fue exitoso
    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'completed';
    }

    // Accessor para verificar si el pago está pendiente
    public function getIsPendingAttribute()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    // Método para marcar como pagado
    public function markAsPaid()
    {
        $this->status = 'completed';
        $this->paid_at = now();
        $this->save();
    }

    // Método para marcar como fallido
    public function markAsFailed($errorMessage = null)
    {
        $this->status = 'failed';
        if ($errorMessage) {
            $this->error_message = $errorMessage;
        }
        $this->save();
    }

    // Método para obtener el total de reembolsos
    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->where('status', 'completed')->sum('amount');
    }

    // Método para verificar si se puede reembolsar
    public function canBeRefunded()
    {
        return $this->is_successful && $this->total_refunded < $this->amount;
    }
}
