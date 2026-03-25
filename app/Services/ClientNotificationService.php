<?php

namespace App\Services;

use App\Models\ClientNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class ClientNotificationService
{
    /**
     * Create a notification for a specific user
     */
    public function createNotification(User $user, string $type, string $title, string $message, ?array $data = null): ClientNotification
    {
        return ClientNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for multiple users
     */
    public function createBulkNotifications(Collection $users, string $type, string $title, string $message, ?array $data = null): int
    {
        $count = 0;
        foreach ($users as $user) {
            $this->createNotification($user, $type, $title, $message, $data);
            $count++;
        }
        return $count;
    }

    /**
     * Notify client of status change
     */
    public function notifyStatusChange(User $user, string $oldStatus, string $newStatus, string $updatedBy): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'status_change',
            title: 'Status Updated',
            message: "Your status has been updated from {$oldStatus} to {$newStatus}",
            data: [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => $updatedBy,
            ]
        );
    }

    /**
     * Notify client that agent added a note
     */
    public function notifyAgentNote(User $user, int $noteId, string $agentName): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'agent_note',
            title: 'New Note Added',
            message: "{$agentName} added a note to your profile",
            data: [
                'note_id' => $noteId,
                'agent_name' => $agentName,
            ]
        );
    }

    /**
     * Notify client of document upload
     */
    public function notifyDocumentUpload(User $user, string $documentName, string $uploadedBy): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'document_uploaded',
            title: 'New Document Available',
            message: "{$uploadedBy} uploaded a new document: {$documentName}",
            data: [
                'document_name' => $documentName,
                'uploaded_by' => $uploadedBy,
            ]
        );
    }

    /**
     * Notify client of new message from agent
     */
    public function notifyNewMessage(User $user, string $messagePreview, string $fromName): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'new_message',
            title: 'New Message',
            message: "{$fromName}: {$messagePreview}",
            data: [
                'from_name' => $fromName,
                'preview' => $messagePreview,
            ]
        );
    }

    /**
     * Notify client of appointment reminder
     */
    public function notifyAppointmentReminder(User $user, string $appointmentTitle, string $appointmentDate): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'appointment_reminder',
            title: 'Upcoming Appointment',
            message: "Reminder: {$appointmentTitle} on {$appointmentDate}",
            data: [
                'appointment_title' => $appointmentTitle,
                'appointment_date' => $appointmentDate,
            ]
        );
    }

    /**
     * Notify client of property recommendation
     */
    public function notifyPropertyRecommendation(User $user, string $propertyAddress, string $recommendedBy): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'property_recommendation',
            title: 'New Property Recommendation',
            message: "{$recommendedBy} recommended a property: {$propertyAddress}",
            data: [
                'property_address' => $propertyAddress,
                'recommended_by' => $recommendedBy,
            ]
        );
    }

    /**
     * Notify client that their account was approved
     */
    public function notifyAccountApproved(User $user): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'account_approved',
            title: 'Account Approved',
            message: 'Your account has been approved. Welcome to McMullen Properties!',
            data: null
        );
    }

    /**
     * Notify client of welcome message after invitation acceptance
     */
    public function notifyWelcome(User $user): ClientNotification
    {
        return $this->createNotification(
            user: $user,
            type: 'welcome',
            title: 'Welcome to McMullen Properties',
            message: 'Thank you for accepting our invitation. Your agent will be in touch soon.',
            data: null
        );
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(ClientNotification $notification): bool
    {
        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        return $user->clientNotifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Delete a notification
     */
    public function deleteNotification(ClientNotification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->clientNotifications()->unread()->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(User $user, int $limit = 10): Collection
    {
        return $user->clientNotifications()
            ->latest()
            ->limit($limit)
            ->get();
    }
}
