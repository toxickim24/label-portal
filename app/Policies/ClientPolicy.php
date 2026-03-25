<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientNotification;
use App\Models\ClientActivity;

class ClientPolicy
{
    /**
     * Determine if the user can access the client portal
     */
    public function accessClientPortal(User $user): bool
    {
        return $user->hasRole('client') && $user->is_approved && !$user->is_suspended;
    }

    /**
     * Determine if the user can view their own profile
     */
    public function viewOwnProfile(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine if the user can edit their own profile
     */
    public function editOwnProfile(User $user): bool
    {
        return $user->hasRole('client') && $user->is_approved;
    }

    /**
     * Determine if the user can change their own password
     */
    public function changeOwnPassword(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine if the user can view their own activities
     */
    public function viewOwnActivities(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine if the user can view a specific activity
     */
    public function viewActivity(User $user, ClientActivity $activity): bool
    {
        return $user->id === $activity->user_id;
    }

    /**
     * Determine if the user can view their own notifications
     */
    public function viewOwnNotifications(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine if the user can view a specific notification
     */
    public function viewNotification(User $user, ClientNotification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine if the user can mark their notification as read
     */
    public function markNotificationAsRead(User $user, ClientNotification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine if the user can delete their own notification
     */
    public function deleteNotification(User $user, ClientNotification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine if the user can mark all their notifications as read
     */
    public function markAllNotificationsAsRead(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine if the user can view their contact information
     */
    public function viewOwnContact(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine if the user can view notes shared by their agent
     */
    public function viewSharedNotes(User $user): bool
    {
        return $user->hasRole('client') && $user->is_approved;
    }

    /**
     * Determine if the user can download documents shared by their agent
     */
    public function downloadDocuments(User $user): bool
    {
        return $user->hasRole('client') && $user->is_approved;
    }

    /**
     * Determine if the user can contact their agent
     */
    public function contactAgent(User $user): bool
    {
        return $user->hasRole('client') && $user->is_approved;
    }
}
