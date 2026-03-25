<?php

namespace App\Services;

use App\Models\ClientActivity;
use App\Models\User;
use Illuminate\Support\Collection;

class ClientActivityService
{
    /**
     * Log a generic activity
     */
    public function logActivity(User $user, string $activityType, string $description, ?array $data = null): ClientActivity
    {
        return ClientActivity::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'description' => $description,
            'data' => $data,
        ]);
    }

    /**
     * Log login activity
     */
    public function logLogin(User $user, ?string $ipAddress = null): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'login',
            description: 'Logged in to client portal',
            data: [
                'ip_address' => $ipAddress ?? request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );
    }

    /**
     * Log profile view activity
     */
    public function logProfileView(User $user): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'profile_view',
            description: 'Viewed profile page',
            data: null
        );
    }

    /**
     * Log profile update activity
     */
    public function logProfileUpdate(User $user, array $fieldsUpdated): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'profile_update',
            description: 'Updated profile information: ' . implode(', ', $fieldsUpdated),
            data: [
                'fields_updated' => $fieldsUpdated,
            ]
        );
    }

    /**
     * Log password change activity
     */
    public function logPasswordChange(User $user, ?string $ipAddress = null): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'password_change',
            description: 'Changed account password',
            data: [
                'ip_address' => $ipAddress ?? request()->ip(),
            ]
        );
    }

    /**
     * Log agent note activity
     */
    public function logAgentNote(User $user, int $noteId, string $agentName): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'agent_note',
            description: "Your agent added a note to your profile",
            data: [
                'note_id' => $noteId,
                'agent_name' => $agentName,
            ]
        );
    }

    /**
     * Log status change activity
     */
    public function logStatusChange(User $user, string $oldStatus, string $newStatus, string $updatedBy): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'status_change',
            description: "Your status was updated from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus),
            data: [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => $updatedBy,
            ]
        );
    }

    /**
     * Log document download activity
     */
    public function logDocumentDownload(User $user, string $documentName): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'document_download',
            description: "Downloaded document: {$documentName}",
            data: [
                'document_name' => $documentName,
            ]
        );
    }

    /**
     * Log document upload activity (when agent uploads for client)
     */
    public function logDocumentUpload(User $user, string $documentName, string $uploadedBy): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'document_upload',
            description: "{$uploadedBy} uploaded a document: {$documentName}",
            data: [
                'document_name' => $documentName,
                'uploaded_by' => $uploadedBy,
            ]
        );
    }

    /**
     * Log message received activity
     */
    public function logMessageReceived(User $user, string $fromName): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'message_received',
            description: "Received a message from {$fromName}",
            data: [
                'from_name' => $fromName,
            ]
        );
    }

    /**
     * Log property saved activity
     */
    public function logPropertySaved(User $user, string $propertyAddress): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'property_saved',
            description: "Saved property: {$propertyAddress}",
            data: [
                'property_address' => $propertyAddress,
            ]
        );
    }

    /**
     * Log appointment scheduled activity
     */
    public function logAppointmentScheduled(User $user, string $appointmentTitle, string $appointmentDate): ClientActivity
    {
        return $this->logActivity(
            user: $user,
            activityType: 'appointment_scheduled',
            description: "Scheduled appointment: {$appointmentTitle}",
            data: [
                'appointment_title' => $appointmentTitle,
                'appointment_date' => $appointmentDate,
            ]
        );
    }

    /**
     * Get recent activities for a user
     */
    public function getRecentActivities(User $user, int $limit = 10): Collection
    {
        return $user->clientActivities()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by type for a user
     */
    public function getActivitiesByType(User $user, string $type, int $limit = 50): Collection
    {
        return $user->clientActivities()
            ->where('activity_type', $type)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity count for a user
     */
    public function getActivityCount(User $user): int
    {
        return $user->clientActivities()->count();
    }

    /**
     * Delete old activities (cleanup)
     */
    public function deleteOldActivities(int $daysOld = 90): int
    {
        return ClientActivity::where('created_at', '<', now()->subDays($daysOld))->delete();
    }
}
