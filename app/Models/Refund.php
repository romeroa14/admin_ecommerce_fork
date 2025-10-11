<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'payment_id',
        'refund_number',
        'amount',
        'type',
        'status',
        'reason',
        'admin_notes',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // Scope para reembolsos completados
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope para reembolsos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para reembolsos rechazados
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Scope por tipo de reembolso
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessor para verificar si está completado
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    // Accessor para verificar si está pendiente
    public function getIsPendingAttribute()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    // Accessor para obtener el estado en español
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completado',
            'rejected' => 'Rechazado',
            default => 'Desconocido',
        };
    }

    // Accessor para obtener el tipo en español
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'full' => 'Reembolso Total',
            'partial' => 'Reembolso Parcial',
            default => 'Desconocido',
        };
    }

    // Método para generar número de reembolso
    public static function generateRefundNumber()
    {
        $lastRefund = self::orderBy('id', 'desc')->first();
        $number = $lastRefund ? (int) substr($lastRefund->refund_number, -6) + 1 : 1;
        return 'REF-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Método para marcar como completado
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->processed_at = now();
        $this->save();
    }

    // Método para marcar como rechazado
    public function markAsRejected($reason = null)
    {
        $this->status = 'rejected';
        if ($reason) {
            $this->admin_notes = $reason;
        }
        $this->save();
    }

    // Método para verificar si se puede procesar
    public function canBeProcessed()
    {
        return $this->status === 'pending';
    }

    // Método para verificar si se puede rechazar
    public function canBeRejected()
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
