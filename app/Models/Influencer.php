<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Influencer extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'birth',
        'phone',
        'instagram',
        'tiktok',
        'youtube',
        'avatar',
        'bio',
        'active',
        'followers',
        'niche',
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
     * Relacionamento com Candidaturas
     */
    public function campaignApplications()
    {
        return $this->hasMany(CampaignApplication::class);
    }

    /**
     * Relacionamento com Campanhas onde foi selecionado
     */
    public function selectedCampaigns()
    {
        return $this->hasMany(Campaign::class, 'selected_influencer_id');
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
     * Dados protegidos do influencer (ocultados até pagamento)
     */
    public function getProtectedData(): array
    {
        return [
            'id' => $this->id,
            'name' => substr($this->name, 0, 3) . '***',
            'instagram' => $this->instagram ? '@***' : null,
            'tiktok' => $this->tiktok ? '@***' : null,
            'youtube' => $this->youtube ? '@***' : null,
            'avatar' => $this->avatar,
            'bio' => substr($this->bio, 0, 100) . '...',
        ];
    }

    /**
     * Dados completos do influencer (após pagamento)
     */
    public function getFullData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
            'youtube' => $this->youtube,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
        ];
    }
}
