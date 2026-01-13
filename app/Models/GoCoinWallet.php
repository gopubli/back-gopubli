<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoCoinWallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'holder_type',
        'holder_id',
        'balance',
        'total_earned',
        'total_spent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'total_earned' => 'decimal:2',
            'total_spent' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento polimórfico com o dono da carteira
     */
    public function holder()
    {
        return $this->morphTo();
    }

    /**
     * Relacionamento com transações
     */
    public function transactions()
    {
        return $this->hasMany(GoCoinTransaction::class, 'wallet_id');
    }

    /**
     * Adicionar crédito
     */
    public function addCredit(float $amount, string $category, string $description, $related = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->total_earned += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'category' => $category,
            'description' => $description,
            'related_type' => $related ? get_class($related) : null,
            'related_id' => $related?->id,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
        ]);
    }

    /**
     * Debitar valor
     */
    public function debit(float $amount, string $category, string $description, $related = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Saldo insuficiente');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->total_spent += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'category' => $category,
            'description' => $description,
            'related_type' => $related ? get_class($related) : null,
            'related_id' => $related?->id,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
        ]);
    }

    /**
     * Verificar se tem saldo suficiente
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
