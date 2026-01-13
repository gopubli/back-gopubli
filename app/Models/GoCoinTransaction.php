<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoCoinTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'category',
        'description',
        'related_type',
        'related_id',
        'balance_before',
        'balance_after',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento com a carteira
     */
    public function wallet()
    {
        return $this->belongsTo(GoCoinWallet::class, 'wallet_id');
    }

    /**
     * Relacionamento polimórfico com o item relacionado
     */
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
