<?php

namespace App\Models;

use App\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Company extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cnpj',
        'phone',
        'address',
        'logo',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    /**
     * Relacionamento com Campanhas
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Relacionamento com Assinatura
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Relacionamento com Carteira GO Coin
     */
    public function goCoinWallet()
    {
        return $this->morphOne(GoCoinWallet::class, 'holder');
    }

    /**
     * Relacionamento com Aceites de Termos
     */
    public function termsAcceptances()
    {
        return $this->morphMany(TermsAcceptance::class, 'user');
    }

    /**
     * Verificar se aceitou um termo específico
     */
    public function hasAcceptedTerm(string $termType): bool
    {
        return TermsAcceptance::hasAccepted($this, $termType);
    }

    /**
     * Obter ou criar carteira GO Coin
     */
    public function getOrCreateWallet()
    {
        if (!$this->goCoinWallet) {
            return $this->goCoinWallet()->create([
                'balance' => 0,
                'total_earned' => 0,
                'total_spent' => 0,
            ]);
        }

        return $this->goCoinWallet;
    }

    /**
     * Verificar se a assinatura está ativa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription && $this->subscription->status === 'active';
    }
}
