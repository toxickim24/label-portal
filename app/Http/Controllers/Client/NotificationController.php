<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get filter from query string
        $filter = $request->input('filter', 'all'); // all, unread, read

        $query = $user->clientNotifications()->latest();

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        $notifications = $query->paginate(15);

        $stats = [
            'total' => $user->clientNotifications()->count(),
            'unread' => $user->clientNotifications()->unread()->count(),
            'read' => $user->clientNotifications()->read()->count(),
        ];

        return Inertia::render('Client/Notifications', [
            'notifications' => $notifications,
            'stats' => $stats,
            'currentFilter' => $filter,
        ]);
    }

    /**
     * Get recent unread notifications for dropdown
     */
    public function recent(Request $request)
    {
        $user = $request->user();

        $notifications = $user->clientNotifications()
            ->unread()
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->clientNotifications()->unread()->count(),
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(ClientNotification $notification): RedirectResponse
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->clientNotifications()
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification
     */
    public function destroy(ClientNotification $notification): RedirectResponse
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
