<script setup>
import { computed } from 'vue';

const props = defineProps({
    activities: {
        type: Array,
        default: () => [],
    },
    limit: {
        type: Number,
        default: 5,
    },
});

const limitedActivities = computed(() => {
    return props.activities.slice(0, props.limit);
});

const getActivityIcon = (type) => {
    switch (type) {
        case 'login':
            return 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1';
        case 'profile_view':
            return 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z';
        case 'profile_update':
            return 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';
        case 'password_change':
            return 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z';
        case 'agent_note':
            return 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z';
        case 'document_download':
            return 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
        case 'status_change':
            return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        default:
            return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }
};

const getActivityColor = (type) => {
    switch (type) {
        case 'login':
            return 'text-blue-500 dark:text-blue-400';
        case 'profile_view':
            return 'text-indigo-500 dark:text-indigo-400';
        case 'profile_update':
            return 'text-indigo-500 dark:text-indigo-400';
        case 'password_change':
            return 'text-orange-500 dark:text-orange-400';
        case 'agent_note':
            return 'text-cyan-500 dark:text-cyan-400';
        case 'document_download':
            return 'text-green-500 dark:text-green-400';
        case 'status_change':
            return 'text-purple-500 dark:text-purple-400';
        default:
            return 'text-gray-500 dark:text-gray-400';
    }
};

const formatTime = (date) => {
    const now = new Date();
    const activityDate = new Date(date);
    const diffMs = now - activityDate;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

    return activityDate.toLocaleDateString();
};
</script>

<template>
    <div class="flow-root">
        <ul v-if="limitedActivities.length > 0" role="list" class="-mb-8">
            <li v-for="(activity, index) in limitedActivities" :key="activity.id">
                <div class="relative pb-8">
                    <span
                        v-if="index !== limitedActivities.length - 1"
                        class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                        aria-hidden="true"
                    />
                    <div class="relative flex space-x-3">
                        <div>
                            <span
                                :class="[
                                    getActivityColor(activity.activity_type),
                                    'flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 ring-8 ring-white dark:ring-gray-900',
                                ]"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActivityIcon(activity.activity_type)" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ activity.description }}
                                </p>
                                <p v-if="activity.data && activity.data.ip_address" class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    From {{ activity.data.ip_address }}
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                <time :datetime="activity.created_at">
                                    {{ formatTime(activity.created_at) }}
                                </time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div v-else class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                No recent activity
            </p>
        </div>
    </div>
</template>
