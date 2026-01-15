<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'influencer_id',
        'offered_amount',
        'proposal_message',
        'status',
        'rejection_reason',
        'accepted_at',
        'rejected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'offered_amount' => 'decimal:2',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Campaign
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Relacionamento com Influencer
     */
    public function influencer()
    {
        return $this->belongsTo(Influencer::class);
    }

    /**
     * Aceitar a candidatura
     */
    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Atualizar a campanha com o influencer selecionado
        $this->campaign->update([
            'selected_influencer_id' => $this->influencer_id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Rejeitar outras candidaturas
        $this->campaign->applications()
            ->where('id', '!=', $this->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => 'Outro influencer foi selecionado',
            ]);
    }

    /**
     * Rejeitar a candidatura
     */
    public function reject(string $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Retirar a candidatura
     */
    public function withdraw()
    {
        $this->delete();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
