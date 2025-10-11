<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'billing_address',
        'notes',
        'pdf_path',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scope para facturas pagadas
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Scope para facturas pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'sent');
    }

    // Scope para facturas vencidas
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // Scope para facturas por fecha
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    // Accessor para verificar si está pagada
    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    // Accessor para verificar si está vencida
    public function getIsOverdueAttribute()
    {
        return $this->status === 'overdue' || 
               ($this->due_date && $this->due_date < now() && $this->status !== 'paid');
    }

    // Accessor para obtener el estado en español
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Borrador',
            'sent' => 'Enviada',
            'paid' => 'Pagada',
            'overdue' => 'Vencida',
            'cancelled' => 'Cancelada',
            default => 'Desconocido',
        };
    }

    // Método para generar número de factura
    public static function generateInvoiceNumber()
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -6) + 1 : 1;
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Método para marcar como pagada
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->save();
    }

    // Método para marcar como vencida
    public function markAsOverdue()
    {
        $this->status = 'overdue';
        $this->save();
    }

    // Método para verificar si se puede cancelar
    public function canBeCancelled()
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    // Método para cancelar factura
    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->status = 'cancelled';
            $this->save();
        }
    }
}
