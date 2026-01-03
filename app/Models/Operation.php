<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Operation extends Model
{
    use BelongsToTenant;

    const TYPE_INCOME = 'INCOME';
    const TYPE_EXPENSE = 'EXPENSE';
    const TYPE_EXCHANGE = 'EXCHANGE';

    const STATUS_PENDING = 'PENDING';
    const STATUS_VALIDATED = 'VALIDATED';
    const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'tenant_id',
        'cashbox_id',
        'target_cashbox_id',
        'account_id',
        'beneficiary_id',
        'type',
        'operation_date',
        'original_amount',
        'currency_id',
        'exchange_rate_used',
        'converted_amount',
        'reference',
        'description',
        'attachment_path',
        'status',
        'created_by',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'operation_date' => 'date',
        'exchange_rate_used' => 'decimal:6',
        'converted_amount' => 'decimal:2',
        'validated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (Operation $operation) {
            // Strict Validation Rules
            if (empty($operation->description)) {
                throw ValidationException::withMessages(['description' => 'La description est obligatoire.']);
            }

            if ($operation->type === self::TYPE_EXCHANGE) {
                if (!$operation->target_cashbox_id) {
                    throw ValidationException::withMessages(['target_cashbox_id' => 'La caisse cible est obligatoire pour un transfert.']);
                }
                if ($operation->cashbox_id == $operation->target_cashbox_id) {
                    throw ValidationException::withMessages(['target_cashbox_id' => 'La caisse source et cible doivent être différentes.']);
                }
                $operation->account_id = null; // Forced null for transfers
            } else {
                // INCOME or EXPENSE
                if (!$operation->account_id) {
                    throw ValidationException::withMessages(['account_id' => 'Le compte comptable est obligatoire pour cette opération.']);
                }

                $account = Account::find($operation->account_id);
                if (!$account) {
                    throw ValidationException::withMessages(['account_id' => 'Compte invalide.']);
                }

                if (!$account->is_active) {
                    throw ValidationException::withMessages(['account_id' => 'Le compte sélectionné est inactif.']);
                }

                if ($operation->type === self::TYPE_INCOME && $account->type !== Account::TYPE_INCOME) {
                    throw ValidationException::withMessages(['account_id' => 'Le compte doit être un compte de RECETTE.']);
                }

                if ($operation->type === self::TYPE_EXPENSE && $account->type !== Account::TYPE_EXPENSE) {
                    throw ValidationException::withMessages(['account_id' => 'Le compte doit être un compte de DÉPENSE.']);
                }
            }
        });

        // Enforce immutability of VALIDATED operations
        static::updating(function (Operation $operation) {
            if ($operation->getOriginal('status') === self::STATUS_VALIDATED) {
                throw ValidationException::withMessages([
                    'status' => 'Une opération validée ne peut plus être modifiée.',
                ]);
            }
        });

        static::deleting(function (Operation $operation) {
            if ($operation->status === self::STATUS_VALIDATED) {
                throw ValidationException::withMessages([
                    'status' => 'Une opération validée ne peut pas être supprimée.',
                ]);
            }
        });
    }

    public function cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function targetCashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class, 'target_cashbox_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Scope for validated operations only (Main accounting rule)
     */
    public function scopeValidated($query)
    {
        return $query->where('status', self::STATUS_VALIDATED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', self::TYPE_INCOME);
    }

    public function scopeExpense($query)
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }
}
