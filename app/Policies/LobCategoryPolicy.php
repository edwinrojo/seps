<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\LobCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LobCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Twg || $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LobCategory $lobCategory): bool
    {
        return $user->role === UserRole::Twg || $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LobCategory $lobCategory): bool
    {
        return $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LobCategory $lobCategory): bool
    {
        return $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LobCategory $lobCategory): bool
    {
        return $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LobCategory $lobCategory): bool
    {
        return $user->role === UserRole::Administrator;
    }
}
