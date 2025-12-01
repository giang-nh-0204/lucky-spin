<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpinSession extends Model
{
    protected $fillable = [
        'session_token',
        'code_id',
        'spin_balance',
        'total_spins',
        'ip_address',
        'user_agent',
        'expires_at',
        'last_spin_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_spin_at' => 'datetime',
        'spin_balance' => 'integer',
        'total_spins' => 'integer',
    ];

    protected $hidden = [
        'session_token', // Không trả về trong response thông thường
    ];

    public function code(): BelongsTo
    {
        return $this->belongsTo(SpinCode::class, 'code_id');
    }

    public function spinResults(): HasMany
    {
        return $this->hasMany(SpinResult::class, 'session_id');
    }

    /**
     * Kiểm tra session còn hiệu lực
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Kiểm tra còn lượt quay
     */
    public function hasSpins(): bool
    {
        return $this->spin_balance > 0;
    }

    /**
     * Trừ 1 lượt quay
     */
    public function decrementSpin(): void
    {
        $this->decrement('spin_balance');
        $this->increment('total_spins');
        $this->update(['last_spin_at' => now()]);
    }

    /**
     * Thêm lượt quay
     */
    public function addSpins(int $amount): void
    {
        $this->increment('spin_balance', $amount);
    }
}
