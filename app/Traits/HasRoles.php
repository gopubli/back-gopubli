<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoles
{
    /**
     * Papéis do usuário
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'user', 'role_user');
    }

    /**
     * Atribui um ou mais papéis ao usuário
     */
    public function assignRole(...$roles): self
    {
        $roleIds = collect($roles)->flatten()->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            return Role::where('name', $role)->firstOrFail()->id;
        });

        $this->roles()->syncWithoutDetaching($roleIds);

        return $this;
    }

    /**
     * Remove um ou mais papéis do usuário
     */
    public function removeRole(...$roles): self
    {
        $roleIds = collect($roles)->flatten()->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            return Role::where('name', $role)->first()?->id;
        })->filter();

        $this->roles()->detach($roleIds);

        return $this;
    }

    /**
     * Verifica se o usuário possui um papel específico
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        if ($role instanceof Role) {
            return $this->roles()->where('id', $role->id)->exists();
        }

        if (is_array($role)) {
            return $this->roles()->whereIn('name', $role)->exists();
        }

        return false;
    }

    /**
     * Verifica se o usuário possui algum dos papéis especificados
     */
    public function hasAnyRole(...$roles): bool
    {
        $roleNames = collect($roles)->flatten()->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Verifica se o usuário possui todos os papéis especificados
     */
    public function hasAllRoles(...$roles): bool
    {
        $roleNames = collect($roles)->flatten()->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $this->roles()->whereIn('name', $roleNames)->count() === $roleNames->count();
    }

    /**
     * Verifica se o usuário possui uma permissão específica
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName)
                    ->where('active', true);
            })
            ->where('active', true)
            ->exists();
    }

    /**
     * Verifica se o usuário possui alguma das permissões especificadas
     */
    public function hasAnyPermission(...$permissions): bool
    {
        $permissionNames = collect($permissions)->flatten();

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionNames) {
                $query->whereIn('name', $permissionNames)
                    ->where('active', true);
            })
            ->where('active', true)
            ->exists();
    }

    /**
     * Verifica se o usuário possui todas as permissões especificadas
     */
    public function hasAllPermissions(...$permissions): bool
    {
        $permissionNames = collect($permissions)->flatten();

        foreach ($permissionNames as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retorna todas as permissões do usuário
     */
    public function getAllPermissions()
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('id', $this->roles->pluck('id'));
        })->get();
    }

    /**
     * Retorna os menus disponíveis para o usuário
     */
    public function getAvailableMenus()
    {
        $permissions = $this->getAllPermissions();
        $permissionIds = $permissions->pluck('id');

        return \App\Models\Menu::active()
            ->root()
            ->with(['children' => function ($query) use ($permissionIds) {
                $query->whereHas('permissions', function ($q) use ($permissionIds) {
                    $q->whereIn('permission_id', $permissionIds);
                })->orWhereDoesntHave('permissions');
            }])
            ->whereHas('permissions', function ($query) use ($permissionIds) {
                $query->whereIn('permission_id', $permissionIds);
            })
            ->orWhereDoesntHave('permissions') // Menus públicos sem permissão
            ->orderBy('order')
            ->get();
    }
}
