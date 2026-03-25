<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view contacts
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contact $contact): bool
    {
        // Admins and managers can view all contacts
        if ($user->hasAnyRole(['admin', 'agent'])) {
            return true;
        }

        // Users can view contacts assigned to them
        return $contact->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create contacts
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contact $contact): bool
    {
        // Admins and managers can update any contact
        if ($user->hasAnyRole(['admin', 'agent'])) {
            return true;
        }

        // Users can update contacts assigned to them
        return $contact->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contact $contact): bool
    {
        // Only admins and managers can delete contacts
        return $user->hasAnyRole(['admin', 'agent']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contact $contact): bool
    {
        // Only admins and managers can restore contacts
        return $user->hasAnyRole(['admin', 'agent']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contact $contact): bool
    {
        // Only admins can permanently delete contacts
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can assign contacts.
     */
    public function assign(User $user, Contact $contact): bool
    {
        // Only admins and managers can assign contacts
        return $user->hasAnyRole(['admin', 'agent']);
    }
}
