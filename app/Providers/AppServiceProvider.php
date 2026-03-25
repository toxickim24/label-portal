<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogClientActivity;
use App\Policies\ClientPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Fix for MySQL utf8mb4 index length issue
        Schema::defaultStringLength(191);

        // Register event listeners
        Event::listen(
            Login::class,
            LogClientActivity::class,
        );

        // Register client policy gates
        Gate::define('access-client-portal', [ClientPolicy::class, 'accessClientPortal']);
        Gate::define('view-own-profile', [ClientPolicy::class, 'viewOwnProfile']);
        Gate::define('edit-own-profile', [ClientPolicy::class, 'editOwnProfile']);
        Gate::define('change-own-password', [ClientPolicy::class, 'changeOwnPassword']);
        Gate::define('view-own-activities', [ClientPolicy::class, 'viewOwnActivities']);
        Gate::define('view-own-notifications', [ClientPolicy::class, 'viewOwnNotifications']);
        Gate::define('mark-all-notifications-as-read', [ClientPolicy::class, 'markAllNotificationsAsRead']);
        Gate::define('view-own-contact', [ClientPolicy::class, 'viewOwnContact']);
        Gate::define('view-shared-notes', [ClientPolicy::class, 'viewSharedNotes']);
        Gate::define('download-documents', [ClientPolicy::class, 'downloadDocuments']);
        Gate::define('contact-agent', [ClientPolicy::class, 'contactAgent']);
    }
}
