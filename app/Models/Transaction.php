<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public const TYPE_CREDIT = 'Credit';
    public const TYPE_DEBIT = 'Debit';

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_DECLINED = 2;

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_amount' => 'decimal:2',
        'transaction_status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->transaction_status === self::STATUS_PENDING;
    }

    public function statusLabel(): string
    {
        return match ($this->transaction_status) {
            self::STATUS_APPROVED => 'Completed',
            self::STATUS_DECLINED => 'Declined',
            default => 'Pending',
        };
    }

    /**
     * Generate a unique, human-readable reference such as "PAY8F3K2Q9X1".
     */
    public static function makeReference(string $prefix): string
    {
        do {
            $ref = strtoupper($prefix) . strtoupper(substr(bin2hex(random_bytes(6)), 0, 10));
        } while (static::where('transaction_ref', $ref)->exists());

        return $ref;
    }
}
