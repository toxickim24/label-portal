<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const approveUser = () => {
    if (confirm('Are you sure you want to approve this user?')) {
        router.post(route('admin.users.approve', props.user.id));
    }
};

const suspendUser = () => {
    if (confirm('Are you sure you want to suspend this user?')) {
        router.post(route('admin.users.suspend', props.user.id));
    }
};

const unsuspendUser = () => {
    if (confirm('Are you sure you want to unsuspend this user?')) {
        router.post(route('admin.users.unsuspend', props.user.id));
    }
};

const deleteUser = () => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', props.user.id));
    }
};

const getStatusBadgeClass = () => {
    if (props.user.deleted_at) {
        return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
    if (props.user.is_suspended) {
        return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
    }
    if (!props.user.is_approved) {
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
    }
    return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
};

const getStatusText = () => {
    if (props.user.deleted_at) return 'Deleted';
    if (props.user.is_suspended) return 'Suspended';
    if (!props.user.is_approved) return 'Pending Approval';
    return 'Active';
};
</script>

<template>
    <Head title="User Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    User Details
                </h2>
                <div class="flex gap-2">
                    <Link
                        v-if="!user.deleted_at"
                        :href="route('admin.users.edit', user.id)"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Edit User
                    </Link>
                    <Link
                        :href="route('admin.users.index')"
                        class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-700"
                    >
                        Back to Users
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- User Information Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="mb-6 flex items-start justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                            </div>
                            <span :class="getStatusBadgeClass()" class="inline-flex rounded-full px-3 py-1 text-sm font-semibold">
                                {{ getStatusText() }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <span class="inline-flex rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400">
                                        {{ user.roles[0]?.name || 'No Role' }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ user.email_verified_at ? 'Yes' : 'No' }}
                                    <span v-if="user.email_verified_at" class="text-gray-500 dark:text-gray-400">
                                        ({{ new Date(user.email_verified_at).toLocaleDateString() }})
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Joined</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(user.created_at).toLocaleString() }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(user.updated_at).toLocaleString() }}
                                </dd>
                            </div>

                            <div v-if="user.approved_at">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(user.approved_at).toLocaleString() }}
                                    <span v-if="user.approved_by" class="text-gray-500 dark:text-gray-400">
                                        by {{ user.approved_by.name }}
                                    </span>
                                </dd>
                            </div>

                            <div v-if="user.suspended_at">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Suspended</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(user.suspended_at).toLocaleString() }}
                                    <span v-if="user.suspended_by" class="text-gray-500 dark:text-gray-400">
                                        by {{ user.suspended_by.name }}
                                    </span>
                                </dd>
                            </div>

                            <div v-if="user.deleted_at">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deleted</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(user.deleted_at).toLocaleString() }}
                                </dd>
                            </div>
                        </dl>

                        <!-- Actions -->
                        <div v-if="!user.deleted_at" class="mt-6 flex gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                            <button
                                v-if="!user.is_approved"
                                @click="approveUser"
                                class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                            >
                                Approve User
                            </button>
                            <button
                                v-if="user.is_approved && !user.is_suspended"
                                @click="suspendUser"
                                class="rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500"
                            >
                                Suspend User
                            </button>
                            <button
                                v-if="user.is_suspended"
                                @click="unsuspendUser"
                                class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                            >
                                Unsuspend User
                            </button>
                            <button
                                @click="deleteUser"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                            >
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Permissions Card -->
                <div v-if="user.permissions && user.permissions.length > 0" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Permissions</h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="permission in user.permissions"
                                :key="permission.id"
                                class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                            >
                                {{ permission.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Activity Log Card -->
                <div v-if="user.activities && user.activities.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                        <div class="space-y-4">
                            <div
                                v-for="activity in user.activities"
                                :key="activity.id"
                                class="flex items-start justify-between border-b border-gray-200 pb-4 last:border-0 dark:border-gray-700"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ activity.description }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ new Date(activity.created_at).toLocaleString() }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ activity.log_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
