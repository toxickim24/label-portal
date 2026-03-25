<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\ClientActivityService;

class LogClientActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected ClientActivityService $activityService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Only log activity for clients
        if ($user && $user->hasRole('client')) {
            $this->activityService->logLogin($user, request()->ip());
        }
    }
}
