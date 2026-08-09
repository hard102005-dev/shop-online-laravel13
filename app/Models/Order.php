<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'];

    public const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'notes',
        'subtotal',
        'shipping_fee',
        'total',
        'status',
        'payment_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        return match ($this->status) {
            'pending' => in_array($newStatus, ['confirmed', 'processing', 'cancelled'], true),
            'confirmed' => in_array($newStatus, ['processing', 'shipped', 'cancelled'], true),
            'processing' => in_array($newStatus, ['shipped', 'completed', 'cancelled'], true),
            'shipped' => in_array($newStatus, ['completed', 'cancelled'], true),
            default => false,
        };
    }
}
