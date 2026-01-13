<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'monthly_amount',
        'status',
        'current_period_start',
        'current_period_end',
        'next_billing_date',
        'days_overdue',
        'suspended_at',
        'suspension_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'current_period_start' => 'date',
            'current_period_end' => 'date',
            'next_billing_date' => 'date',
            'suspended_at' => 'datetime',
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
     * Ativar assinatura
     */
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'next_billing_date' => now()->addMonth(),
            'days_overdue' => 0,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
    }

    /**
     * Suspender assinatura
     */
    public function suspend(string $reason)
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);

        // Bloquear todas as campanhas ativas da empresa
        $this->company->campaigns()
            ->whereIn('status', ['open', 'in_progress'])
            ->each(function ($campaign) use ($reason) {
                $campaign->block($reason);
            });
    }

    /**
     * Renovar assinatura
     */
    public function renew()
    {
        $this->update([
            'status' => 'active',
            'current_period_start' => $this->next_billing_date,
            'current_period_end' => $this->next_billing_date->copy()->addMonth(),
            'next_billing_date' => $this->next_billing_date->copy()->addMonth(),
            'days_overdue' => 0,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        // Desbloquear campanhas da empresa
        $this->company->campaigns()
            ->blocked()
            ->each(function ($campaign) {
                $campaign->unblock();
            });
    }

    /**
     * Verificar se está vencida
     */
    public function isOverdue(): bool
    {
        return $this->next_billing_date && 
               $this->next_billing_date->isPast() && 
               $this->status !== 'active';
    }

    /**
     * Calcular dias de atraso
     */
    public function calculateDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->next_billing_date);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_billing_date', '<', now())
                    ->where('status', '!=', 'active');
    }
}
