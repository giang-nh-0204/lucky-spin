<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    protected $fillable = [
        'name',
        'price',
        'image',
        'color',
        'probability',
        'is_active',
        'stock',
        'sort_order',
    ];

    protected $casts = [
        'probability' => 'decimal:4',
        'is_active' => 'boolean',
        'price' => 'integer',
        'stock' => 'integer',
    ];

    protected $hidden = [
        'probability', // Không expose ra API public
    ];

    protected $appends = ['image_url'];

    /**
     * Trả về URL đầy đủ của ảnh
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Nếu đã là URL đầy đủ
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Thêm base URL
        return url($this->image);
    }

    public function spinResults(): HasMany
    {
        return $this->hasMany(SpinResult::class);
    }

    /**
     * Scope: Chỉ lấy prizes đang active và còn stock
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('stock')->orWhere('stock', '>', 0);
            });
    }

    /**
     * Giảm stock khi trúng giải
     */
    public function decrementStock(): void
    {
        if ($this->stock !== null) {
            $this->decrement('stock');
        }
    }
}
