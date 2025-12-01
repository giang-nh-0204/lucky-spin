<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinResult extends Model
{
    protected $fillable = [
        'session_id',
        'prize_id',
        'spin_token',
        'target_angle',
        'status',
        'claimed_at',
        'delivery_status',
        'delivery_note',
        'delivered_at',
    ];

    protected $casts = [
        'target_angle' => 'decimal:2',
        'claimed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(SpinSession::class, 'session_id');
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * Kiểm tra có thể claim không
     */
    public function canClaim(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Đánh dấu đã claim
     */
    public function markAsClaimed(): void
    {
        $this->update([
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);
    }

    /**
     * Đánh dấu hết hạn
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Đánh dấu đã gửi quà
     */
    public function markAsDelivered(?string $note = null): void
    {
        $this->update([
            'delivery_status' => 'delivered',
            'delivery_note' => $note,
            'delivered_at' => now(),
        ]);
    }

    /**
     * Đánh dấu chưa gửi quà
     */
    public function markAsUndelivered(?string $note = null): void
    {
        $this->update([
            'delivery_status' => 'pending',
            'delivery_note' => $note,
            'delivered_at' => null,
        ]);
    }
}
