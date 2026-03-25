<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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
const showBulkEditModal = ref(false);

const bulkEditForm = useForm({
    contact_ids: [],
    status: '',
    priority: '',
    assigned_to: '',
    contact_type: '',
    source: '',
    tag_ids: [],
    clear_existing_tags: false,
});

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

const openBulkEditModal = () => {
    if (selectedContacts.value.length === 0) {
        alert('Please select contacts first');
        return;
    }
    bulkEditForm.contact_ids = selectedContacts.value;
    showBulkEditModal.value = true;
};

const closeBulkEditModal = () => {
    showBulkEditModal.value = false;
    bulkEditForm.reset();
};

const submitBulkEdit = () => {
    bulkEditForm.post(route('contacts.bulk-update'), {
        onSuccess: () => {
            closeBulkEditModal();
            selectedContacts.value = [];
        },
    });
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
                <div v-if="selectedContacts.length > 0 || contacts.data.length > 0" class="mb-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedContacts.length }} selected
                        </span>
                        <button
                            v-if="selectedContacts.length === 0"
                            @click="toggleSelectAll"
                            class="rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                        >
                            Select All
                        </button>
                        <button
                            v-else
                            @click="selectedContacts = []"
                            class="rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                        >
                            Deselect All
                        </button>
                        <button
                            v-if="selectedContacts.length > 0"
                            @click="openBulkEditModal"
                            class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                        >
                            Bulk Edit
                        </button>
                        <button
                            v-if="selectedContacts.length > 0"
                            @click="bulkDelete"
                            class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                        >
                            Delete Selected
                        </button>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="block lg:hidden space-y-4">
                    <div
                        v-for="contact in contacts.data"
                        :key="contact.id"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4"
                    >
                        <!-- Card Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start gap-3 flex-1">
                                <input
                                    type="checkbox"
                                    :value="contact.id"
                                    v-model="selectedContacts"
                                    class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                        {{ contact.first_name }} {{ contact.last_name }}
                                    </h3>
                                    <p v-if="contact.contact_type" class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                                        {{ contact.contact_type }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="space-y-2 mb-3">
                            <div v-if="contact.email" class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300 truncate">{{ contact.email }}</span>
                            </div>
                            <div v-if="contact.phone" class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">{{ contact.phone }}</span>
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span :class="getStatusBadgeClass(contact.status)" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize">
                                {{ contact.status }}
                            </span>
                            <span :class="getPriorityBadgeClass(contact.priority)" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize">
                                {{ contact.priority }}
                            </span>
                            <span v-if="contact.assigned_user" class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ contact.assigned_user.name }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                :href="route('contacts.show', contact.id)"
                                class="flex-1 text-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:hover:bg-indigo-900/30"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('contacts.edit', contact.id)"
                                class="flex-1 text-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                            >
                                Edit
                            </Link>
                            <button
                                v-if="!contact.deleted_at"
                                @click="deleteContact(contact.id)"
                                class="flex-1 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                            >
                                Delete
                            </button>
                            <button
                                v-else
                                @click="restoreContact(contact.id)"
                                class="flex-1 px-3 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30"
                            >
                                Restore
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    <input
                                        type="checkbox"
                                        :checked="selectedContacts.length === contacts.data.length && contacts.data.length > 0"
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
                                    <div v-if="contact.contact_type" class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                                        {{ contact.contact_type }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div v-if="contact.email" class="text-sm text-gray-900 dark:text-white">{{ contact.email }}</div>
                                    <div v-if="contact.phone" class="text-sm text-gray-500 dark:text-gray-400">{{ contact.phone }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span :class="getStatusBadgeClass(contact.status)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 capitalize">
                                        {{ contact.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span :class="getPriorityBadgeClass(contact.priority)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 capitalize">
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

                </div>

                <!-- Pagination (shared by both views) -->
                <div class="mt-4 border-t border-gray-200 bg-white rounded-lg px-4 py-3 shadow dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ contacts.from || 0 }} to {{ contacts.to || 0 }} of {{ contacts.total }} results
                        </div>
                        <div class="flex gap-2 flex-wrap justify-center">
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

        <!-- Bulk Edit Modal -->
        <div v-if="showBulkEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeBulkEditModal"></div>

                <!-- Modal panel -->
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
                    <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">
                                Bulk Edit Contacts
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Editing {{ selectedContacts.length }} contact(s). Leave fields empty to keep existing values.
                            </p>
                        </div>

                        <form @submit.prevent="submitBulkEdit" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Status
                                    </label>
                                    <select
                                        v-model="bulkEditForm.status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="">-- No Change --</option>
                                        <option value="lead">Lead</option>
                                        <option value="prospect">Prospect</option>
                                        <option value="active">Active</option>
                                        <option value="closed">Closed</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>

                                <!-- Priority -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Priority
                                    </label>
                                    <select
                                        v-model="bulkEditForm.priority"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="">-- No Change --</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>

                                <!-- Contact Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Contact Type
                                    </label>
                                    <select
                                        v-model="bulkEditForm.contact_type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="">-- No Change --</option>
                                        <option value="buyer">Buyer</option>
                                        <option value="seller">Seller</option>
                                        <option value="investor">Investor</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Assigned To -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Assigned To
                                    </label>
                                    <select
                                        v-model="bulkEditForm.assigned_to"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="">-- No Change --</option>
                                        <option v-for="user in users" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Source -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Source
                                </label>
                                <input
                                    v-model="bulkEditForm.source"
                                    type="text"
                                    placeholder="Leave empty for no change"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                />
                            </div>

                            <!-- Tags -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tags
                                </label>

                                <!-- Clear existing tags option -->
                                <div class="mb-3">
                                    <label class="flex items-center">
                                        <input
                                            v-model="bulkEditForm.clear_existing_tags"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            Clear all existing tags first
                                        </span>
                                    </label>
                                    <p class="ml-6 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Check this to remove all existing tags before adding new ones (if any selected below)
                                    </p>
                                </div>

                                <div class="space-y-2 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label
                                        v-for="tag in tags"
                                        :key="tag.id"
                                        class="flex items-center"
                                    >
                                        <input
                                            v-model="bulkEditForm.tag_ids"
                                            type="checkbox"
                                            :value="tag.id"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                        />
                                        <span class="ml-2 flex items-center gap-2">
                                            <span
                                                class="inline-block w-3 h-3 rounded-full"
                                                :style="{ backgroundColor: tag.color }"
                                            ></span>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ tag.name }}</span>
                                        </span>
                                    </label>
                                    <div v-if="tags.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                                        No tags available.
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                                <button
                                    type="submit"
                                    :disabled="bulkEditForm.processing"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 sm:w-auto"
                                >
                                    <span v-if="bulkEditForm.processing">Updating...</span>
                                    <span v-else>Update Contacts</span>
                                </button>
                                <button
                                    type="button"
                                    @click="closeBulkEditModal"
                                    :disabled="bulkEditForm.processing"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
