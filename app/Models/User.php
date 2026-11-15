<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * Privileged fields (user_type, security codes, loan eligibility, status
     * flags) are intentionally NOT listed here; they are only ever set
     * explicitly by the admin controller.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'token',
        'currency',
        'phone_number',
        'a_number',
        'address',
        'country',
        'account_type',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token',
        'otp',
        'ccic_code',
        'int_code',
        'ssn',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'eligible_loan' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function isAdmin(): bool
    {
        return (string) $this->user_type === '1';
    }

    public function isActivated(): bool
    {
        return (string) $this->is_activated === '1';
    }

    /**
     * Full name, so legacy `$user->name` references keep working.
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * The account balance, derived from the transactions ledger.
     *
     * - Credits count only once approved (pending deposits/loans are not spendable).
     * - Debits count while pending or approved, so funds are held for a pending
     *   withdrawal; a declined debit no longer counts, which refunds it.
     */
    public function balance(): float
    {
        $credits = $this->transactions()
            ->where('transaction_type', Transaction::TYPE_CREDIT)
            ->where('transaction_status', Transaction::STATUS_APPROVED)
            ->sum('transaction_amount');

        $debits = $this->transactions()
            ->where('transaction_type', Transaction::TYPE_DEBIT)
            ->whereIn('transaction_status', [Transaction::STATUS_PENDING, Transaction::STATUS_APPROVED])
            ->sum('transaction_amount');

        return round((float) $credits - (float) $debits, 2);
    }
}
