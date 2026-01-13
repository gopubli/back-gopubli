<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'icon',
        'route',
        'url',
        'parent_id',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Menu pai (para hierarquia)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Submenus (filhos)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Permissões necessárias para visualizar este menu
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'menu_permission');
    }

    /**
     * Scope para pegar apenas menus ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para pegar apenas menus raiz (sem pai)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
