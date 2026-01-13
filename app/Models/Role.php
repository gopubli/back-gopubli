<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Permissões associadas ao papel
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Administradores com este papel
     */
    public function administrators(): MorphToMany
    {
        return $this->morphedByMany(Administrator::class, 'user', 'role_user');
    }

    /**
     * Empresas com este papel
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'user', 'role_user');
    }

    /**
     * Verifica se o papel possui uma permissão específica
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Atribui permissões ao papel
     */
    public function givePermissionTo(...$permissions): self
    {
        $permissionIds = collect($permissions)->flatten()->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            return Permission::where('name', $permission)->firstOrFail()->id;
        });

        $this->permissions()->syncWithoutDetaching($permissionIds);

        return $this;
    }

    /**
     * Remove permissões do papel
     */
    public function revokePermissionTo(...$permissions): self
    {
        $permissionIds = collect($permissions)->flatten()->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            return Permission::where('name', $permission)->first()?->id;
        })->filter();

        $this->permissions()->detach($permissionIds);

        return $this;
    }
}
