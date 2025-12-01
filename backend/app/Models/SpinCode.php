<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpinCode extends Model
{
    protected $fillable = [
        'code',
        'spins_amount',
        'max_uses',
        'used_count',
        'note',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'spins_amount' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(SpinSession::class, 'code_id');
    }

    /**
     * Kiểm tra code có thể sử dụng không
     */
    public function isValid(): bool
    {
        // Không active
        if (!$this->is_active) {
            return false;
        }

        // Hết hạn
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Đã dùng hết số lần
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Lấy lý do không hợp lệ
     */
    public function getInvalidReason(): ?string
    {
        if (!$this->is_active) {
            return 'Mã đã bị vô hiệu hóa';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Mã đã hết hạn';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'Mã đã được sử dụng hết';
        }

        return null;
    }

    /**
     * Tăng số lần sử dụng
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
