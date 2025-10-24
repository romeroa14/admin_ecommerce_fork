<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'service_level',
        'status',
        'shipping_cost',
        'shipping_address',
        'notes',
        'shipped_at',
        'delivered_at',
        'estimated_delivery',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'shipping_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scope para envíos entregados
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Scope para envíos en tránsito
    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    // Scope para envíos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope por transportista
    public function scopeByCarrier($query, $carrier)
    {
        return $query->where('carrier', $carrier);
    }

    // Accessor para verificar si está entregado
    public function getIsDeliveredAttribute()
    {
        return $this->status === 'delivered';
    }

    // Accessor para verificar si está en tránsito
    public function getIsInTransitAttribute()
    {
        return $this->status === 'in_transit';
    }

    // Accessor para obtener el estado en español
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'shipped' => 'Enviado',
            'in_transit' => 'En Tránsito',
            'delivered' => 'Entregado',
            'failed' => 'Fallido',
            default => 'Desconocido',
        };
    }

    // Método para marcar como enviado
    public function markAsShipped($trackingNumber = null)
    {
        $this->status = 'shipped';
        $this->shipped_at = now();
        if ($trackingNumber) {
            $this->tracking_number = $trackingNumber;
        }
        $this->save();
    }

    // Método para marcar como entregado
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    // Método para obtener la URL de seguimiento
    public function getTrackingUrlAttribute()
    {
        if (!$this->tracking_number || !$this->carrier) {
            return null;
        }

        return match (strtolower($this->carrier)) {
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$this->tracking_number}",
            'ups' => "https://www.ups.com/track?track=yes&trackNums={$this->tracking_number}",
            'dhl' => "https://www.dhl.com/tracking?trackingNumber={$this->tracking_number}",
            'usps' => "https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1={$this->tracking_number}",
            default => null,
        };
    }

    public static function generateTrackingNumber(): string
    {
        $prefix = 'TRK';
        $year = now()->year;
        $month = now()->format('m');
        $day = now()->format('d');
        
        $lastShipment = self::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastShipment ? (int) substr($lastShipment->tracking_number, -4) + 1 : 1;
        
        return "{$prefix}-{$year}{$month}{$day}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
