<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    contacts: Object,
    statistics: Object,
    tags: Array,
    users: Array,
    filters: Object,
});

const filters = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
    contact_type: props.filters.contact_type || '',
    priority: props.filters.priority || '',
    assigned_to: props.filters.assigned_to || '',
    with_trashed: props.filters.with_trashed || false,
});

const selectedContacts = ref([]);

const updateFilters = debounce(() => {
    router.get(route('contacts.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

watch(filters, updateFilters, { deep: true });

const getStatusBadgeClass = (status) => {
    const classes = {
        lead: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        prospect: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
        active: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        archived: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPriorityBadgeClass = (priority) => {
    const classes = {
        low: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        high: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };
    return classes[priority] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const deleteContact = (contactId) => {
    if (confirm('Are you sure you want to delete this contact?')) {
        router.delete(route('contacts.destroy', contactId));
    }
};

const restoreContact = (contactId) => {
    if (confirm('Are you sure you want to restore this contact?')) {
        router.post(route('contacts.restore', contactId));
    }
};

const toggleSelectAll = () => {
    if (selectedContacts.value.length === props.contacts.data.length) {
        selectedContacts.value = [];
    } else {
        selectedContacts.value = props.contacts.data.map(c => c.id);
    }
};

const bulkDelete = () => {
    if (selectedContacts.value.length === 0) {
        alert('Please select contacts first');
        return;
    }
    if (confirm(`Are you sure you want to delete ${selectedContacts.value.length} contact(s)?`)) {
        router.post(route('contacts.bulk-delete'), {
            contact_ids: selectedContacts.value
        });
        selectedContacts.value = [];
    }
};
</script>

<template>
    <Head title="Contacts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Contact Management
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('contacts.pipeline')"
                        class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                    >
                        Pipeline View
                    </Link>
                    <Link
                        :href="route('contacts.create')"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Add Contact
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Statistics -->
                <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ statistics.total }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Leads</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ statistics.lead }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Prospects</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ statistics.prospect }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Active</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ statistics.active }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Closed</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ statistics.closed }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Search contacts..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select
                                v-model="filters.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            >
                                <option value="">All Statuses</option>
                                <option value="lead">Lead</option>
                                <option value="prospect">Prospect</option>
                                <option value="active">Active</option>
                                <option value="closed">Closed</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select
                                v-model="filters.contact_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            >
                                <option value="">All Types</option>
                                <option value="buyer">Buyer</option>
                                <option value="seller">Seller</option>
                                <option value="investor">Investor</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <select
                                v-model="filters.priority"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            >
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned To</label>
                            <select
                                v-model="filters.assigned_to"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            >
                                <option value="">All Users</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div v-if="selectedContacts.length > 0" class="mb-4 flex items-center gap-3">
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        {{ selectedContacts.length }} selected
                    </span>
                    <button
                        @click="bulkDelete"
                        class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                    >
                        Delete Selected
                    </button>
                </div>

                <!-- Contacts Table -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    <input
                                        type="checkbox"
                                        :checked="selectedContacts.length === contacts.data.length"
                                        @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Contact Info
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Priority
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Assigned To
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            <tr v-for="contact in contacts.data" :key="contact.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        :value="contact.id"
                                        v-model="selectedContacts"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ contact.first_name }} {{ contact.last_name }}
                                    </div>
                                    <div v-if="contact.contact_type" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ contact.contact_type }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div v-if="contact.email" class="text-sm text-gray-900 dark:text-white">{{ contact.email }}</div>
                                    <div v-if="contact.phone" class="text-sm text-gray-500 dark:text-gray-400">{{ contact.phone }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span :class="getStatusBadgeClass(contact.status)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                        {{ contact.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span :class="getPriorityBadgeClass(contact.priority)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                        {{ contact.priority }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ contact.assigned_user?.name || 'Unassigned' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('contacts.show', contact.id)"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="route('contacts.edit', contact.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="!contact.deleted_at"
                                            @click="deleteContact(contact.id)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Delete
                                        </button>
                                        <button
                                            v-else
                                            @click="restoreContact(contact.id)"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        >
                                            Restore
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ contacts.from }} to {{ contacts.to }} of {{ contacts.total }} results
                            </div>
                            <div class="flex gap-2">
                                <template v-for="link in contacts.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'rounded px-3 py-1 text-sm',
                                            link.active
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'
                                        ]"
                                        v-html="link.label"
                                    />
                                    <span
                                        v-else
                                        :class="[
                                            'rounded px-3 py-1 text-sm cursor-not-allowed opacity-50',
                                            'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                        ]"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
