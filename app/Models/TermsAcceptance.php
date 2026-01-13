<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAcceptance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_type',
        'user_id',
        'term_type',
        'term_version',
        'ip_address',
        'user_agent',
        'accepted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento polimórfico com o usuário
     */
    public function user()
    {
        return $this->morphTo();
    }

    /**
     * Verificar se o usuário aceitou um termo específico
     */
    public static function hasAccepted($user, string $termType): bool
    {
        return self::where('user_type', get_class($user))
            ->where('user_id', $user->id)
            ->where('term_type', $termType)
            ->exists();
    }

    /**
     * Registrar aceite de termo
     */
    public static function recordAcceptance($user, string $termType, string $version = '1.0', $request = null)
    {
        return self::create([
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'term_type' => $termType,
            'term_version' => $version,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'accepted_at' => now(),
        ]);
    }
}
