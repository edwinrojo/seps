<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\SiteValidation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SiteValidationPolicy
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
    public function view(User $user, SiteValidation $siteValidation): bool
    {
        return $user->role === UserRole::Twg || $user->role === UserRole::Administrator;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Twg;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiteValidation $siteValidation): bool
    {
        return $user->role === UserRole::Twg;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiteValidation $siteValidation): bool
    {
        return $user->role === UserRole::Twg;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiteValidation $siteValidation): bool
    {
        return $user->role === UserRole::Twg;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiteValidation $siteValidation): bool
    {
        return $user->role === UserRole::Twg;
    }
}
