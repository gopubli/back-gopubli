<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'objective',
        'category',
        'platform',
        'start_date',
        'end_date',
        'requirements',
        'deliverables',
        'amount',
        'min_amount',
        'influencer_amount',
        'gopubli_commission',
        'marketing_budget',
        'status',
        'payment_status',
        'blocked',
        'blocked_reason',
        'selected_influencer_id',
        'started_at',
        'completed_at',
        'payment_confirmed_at',
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
            'min_amount' => 'decimal:2',
            'influencer_amount' => 'decimal:2',
            'gopubli_commission' => 'decimal:2',
            'marketing_budget' => 'decimal:2',
            'blocked' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'payment_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com Influencer selecionado
     */
    public function selectedInfluencer()
    {
        return $this->belongsTo(Influencer::class, 'selected_influencer_id');
    }

    /**
     * Relacionamento com CampaignApplications
     */
    public function applications()
    {
        return $this->hasMany(CampaignApplication::class);
    }

    /**
     * Calcular distribuição de valores (60/20/20)
     */
    public function calculateDistribution()
    {
        $this->influencer_amount = $this->amount * 0.60;
        $this->gopubli_commission = $this->amount * 0.20;
        $this->marketing_budget = $this->amount * 0.20;
        $this->save();
    }

    /**
     * Verificar se a campanha pode ser iniciada
     */
    public function canBeStarted(): bool
    {
        return $this->payment_status === 'paid' && 
               $this->status === 'open' && 
               !$this->blocked;
    }

    /**
     * Bloquear campanha
     */
    public function block(string $reason)
    {
        $this->update([
            'blocked' => true,
            'blocked_reason' => $reason,
            'status' => 'blocked',
        ]);
    }

    /**
     * Desbloquear campanha
     */
    public function unblock()
    {
        $this->update([
            'blocked' => false,
            'blocked_reason' => null,
            'status' => 'open',
        ]);
    }

    /**
     * Scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'open')
                    ->where('blocked', false)
                    ->where('payment_status', 'paid')
                    ->whereNull('selected_influencer_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeBlocked($query)
    {
        return $query->where('blocked', true);
    }
}
