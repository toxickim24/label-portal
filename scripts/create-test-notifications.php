<?php

/**
 * Script to create test notifications for client portal testing
 *
 * Usage: php artisan tinker < scripts/create-test-notifications.php
 * Or run individual commands in tinker
 */

// Find the first client user
$clientUser = App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'client');
})->first();

if (!$clientUser) {
    echo "❌ No client users found. Please create a client invitation first.\n";
    exit;
}

echo "Found client: {$clientUser->name} ({$clientUser->email})\n\n";

// Get the notification service
$notificationService = app(App\Services\ClientNotificationService::class);

echo "=== Creating Test Notifications ===\n\n";

// 1. Status Change
$notificationService->notifyStatusChange(
    $clientUser,
    'lead',
    'qualified',
    'Tim McMullen'
);
echo "✅ Status change notification created\n";

// 2. Agent Note
$notificationService->notifyAgentNote(
    $clientUser,
    rand(1, 100),
    'Sarah Johnson'
);
echo "✅ Agent note notification created\n";

// 3. Document Upload
$notificationService->notifyDocumentUpload(
    $clientUser,
    'Property_Contract_' . date('Y-m-d') . '.pdf',
    'Tim McMullen'
);
echo "✅ Document upload notification created\n";

// 4. New Message
$notificationService->notifyNewMessage(
    $clientUser,
    'Hi! I found some great properties for you...',
    'Tim McMullen'
);
echo "✅ New message notification created\n";

// 5. Appointment Reminder
$notificationService->notifyAppointmentReminder(
    $clientUser,
    'Property Viewing',
    now()->addDays(2)->format('F j, Y \a\t g:i A')
);
echo "✅ Appointment reminder notification created\n";

// 6. Property Recommendation
$notificationService->notifyPropertyRecommendation(
    $clientUser,
    rand(100, 999) . ' Main St, Austin TX',
    'Tim McMullen'
);
echo "✅ Property recommendation notification created\n";

// Summary
$unreadCount = $clientUser->clientNotifications()->unread()->count();
echo "\n=== Summary ===\n";
echo "User: {$clientUser->name}\n";
echo "Email: {$clientUser->email}\n";
echo "Total unread: {$unreadCount}\n";
echo "\n✅ Done! Log in to see the notifications.\n";
