<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'agent']);
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasAnyRole(['admin', 'agent']) || $user->id === $model->id;
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admins can update anyone except themselves (to prevent self-demotion)
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users can update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admins can delete users
        // Cannot delete yourself
        return $user->hasRole('admin') && $user->id !== $model->id;
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $user->id !== $model->id;
    }

    /**
     * Determine if the user can approve other users.
     */
    public function approve(User $user, User $model): bool
    {
        // Only admins can approve
        // Cannot approve yourself
        return $user->hasRole('admin') && $user->id !== $model->id;
    }

    /**
     * Determine if the user can suspend other users.
     */
    public function suspend(User $user, User $model): bool
    {
        // Only admins can suspend
        // Cannot suspend yourself
        return $user->hasRole('admin') && $user->id !== $model->id;
    }
}
