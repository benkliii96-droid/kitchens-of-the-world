<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $guarded = ['id'];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->withTrashed();
    }

    public function stepProgress(): HasMany
    {
        return $this->hasMany(StepProgress::class);
    }

    public function refreshExpiredStatus(): void
    {
        if ($this->status === self::STATUS_PENDING
            && $this->expires_at
            && $this->expires_at->isPast()
        ) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'оплачено',
            self::STATUS_FAILED => 'ошибка оплаты',
            self::STATUS_EXPIRED => 'истекло время на оплату',
            default => 'не оплачено',
        };
    }

    public function canDelete(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_EXPIRED], true);
    }
}
