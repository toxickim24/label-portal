<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    notifications: Object,
    stats: Object,
    currentFilter: {
        type: String,
        default: 'all'
    }
});

const getNotificationIcon = (type) => {
    const icons = {
        'message': 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'property': 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'document': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'appointment': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'status': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'default': 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
    };
    return icons[type] || icons.default;
};

const getNotificationColor = (type) => {
    const colors = {
        'message': 'indigo',
        'property': 'green',
        'document': 'blue',
        'appointment': 'purple',
        'status': 'yellow',
        'default': 'gray'
    };
    return colors[type] || colors.default;
};

const formatDate = (date) => {
    const d = new Date(date);
    const now = new Date();
    const diffMs = now - d;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const markAsRead = (notificationId) => {
    router.post(route('client.notifications.read', notificationId), {}, {
        preserveScroll: true,
    });
};

const markAllAsRead = () => {
    router.post(route('client.notifications.read-all'), {}, {
        preserveScroll: true,
    });
};

const deleteNotification = (notificationId) => {
    if (confirm('Are you sure you want to delete this notification?')) {
        router.delete(route('client.notifications.destroy', notificationId), {
            preserveScroll: true,
        });
    }
};

const changeFilter = (filter) => {
    router.get(route('client.notifications', { filter }), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Notifications" />

    <ClientLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400"
                >
                    {{ $page.props.flash.success }}
                </div>

                <!-- Stats Cards -->
                <div class="mb-6 grid gap-6 md:grid-cols-3">
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Total Notifications
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ stats.total }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20">
                                        <div class="h-2 w-2 rounded-full bg-blue-600 dark:bg-blue-400"></div>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Unread
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ stats.unread }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Read
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ stats.read }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Actions -->
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex gap-2">
                        <button
                            @click="changeFilter('all')"
                            :class="[
                                'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                currentFilter === 'all'
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                            ]"
                        >
                            All
                        </button>
                        <button
                            @click="changeFilter('unread')"
                            :class="[
                                'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                currentFilter === 'unread'
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                            ]"
                        >
                            Unread
                        </button>
                        <button
                            @click="changeFilter('read')"
                            :class="[
                                'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                currentFilter === 'read'
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                            ]"
                        >
                            Read
                        </button>
                    </div>

                    <button
                        v-if="stats.unread > 0"
                        @click="markAllAsRead"
                        class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Mark All as Read
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <ul v-if="notifications.data.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li
                            v-for="notification in notifications.data"
                            :key="notification.id"
                            :class="[
                                'relative p-6 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50',
                                !notification.read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''
                            ]"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div :class="[
                                    'flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full',
                                    `bg-${getNotificationColor(notification.type)}-100 dark:bg-${getNotificationColor(notification.type)}-900/20`
                                ]">
                                    <svg
                                        :class="[
                                            'h-6 w-6',
                                            `text-${getNotificationColor(notification.type)}-600 dark:text-${getNotificationColor(notification.type)}-400`
                                        ]"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getNotificationIcon(notification.type)" />
                                    </svg>
                                </div>

                                <!-- Content -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ notification.title }}
                                            </p>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ notification.message }}
                                            </p>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                {{ formatDate(notification.created_at) }}
                                            </p>
                                        </div>

                                        <!-- Unread Badge -->
                                        <div v-if="!notification.read_at" class="ml-4 flex-shrink-0">
                                            <div class="h-2 w-2 rounded-full bg-blue-600 dark:bg-blue-400"></div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4 flex gap-3">
                                        <button
                                            v-if="!notification.read_at"
                                            @click="markAsRead(notification.id)"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            Mark as Read
                                        </button>
                                        <button
                                            @click="deleteNotification(notification.id)"
                                            class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Empty State -->
                    <div v-else class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ currentFilter === 'unread' ? "You're all caught up!" : 'No notifications to display.' }}
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="notifications.data.length > 0 && (notifications.prev_page_url || notifications.next_page_url)" class="mt-6 flex items-center justify-between">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <Link
                            v-if="notifications.prev_page_url"
                            :href="notifications.prev_page_url"
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="notifications.next_page_url"
                            :href="notifications.next_page_url"
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ notifications.from }}</span>
                                to
                                <span class="font-medium">{{ notifications.to }}</span>
                                of
                                <span class="font-medium">{{ notifications.total }}</span>
                                results
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-if="notifications.prev_page_url"
                                :href="notifications.prev_page_url"
                                class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="notifications.next_page_url"
                                :href="notifications.next_page_url"
                                class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700"
                            >
                                Next
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
