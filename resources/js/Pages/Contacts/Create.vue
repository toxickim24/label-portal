<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    tags: Array,
    users: Array,
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip: '',
    contact_type: '',
    status: 'lead',
    source: '',
    priority: 'medium',
    assigned_to: null,
    tags: [],
    note: '',
});

const submit = () => {
    form.post(route('contacts.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Create Contact" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Create New Contact
                </h2>
                <Link
                    :href="route('contacts.index')"
                    class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                >
                    Back to Contacts
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Basic Information</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                    <div v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                    <div v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">{{ form.errors.last_name }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                    <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Address</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street Address</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                </div>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                                        <input
                                            v-model="form.city"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                                        <input
                                            v-model="form.state"
                                            type="text"
                                            maxlength="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ZIP</label>
                                        <input
                                            v-model="form.zip"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Contact Details</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Type</label>
                                    <select
                                        v-model="form.contact_type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="">Select type...</option>
                                        <option value="buyer">Buyer</option>
                                        <option value="seller">Seller</option>
                                        <option value="investor">Investor</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select
                                        v-model="form.status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="lead">Lead</option>
                                        <option value="prospect">Prospect</option>
                                        <option value="active">Active</option>
                                        <option value="closed">Closed</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Source</label>
                                    <input
                                        v-model="form.source"
                                        type="text"
                                        placeholder="e.g., Referral, Website, Open House"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <select
                                        v-model="form.priority"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign To</label>
                                    <select
                                        v-model="form.assigned_to"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    >
                                        <option :value="null">Unassigned</option>
                                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <label v-for="tag in tags" :key="tag.id" class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        :value="tag.id"
                                        v-model="form.tags"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="ml-2 rounded-full px-3 py-1 text-sm"
                                        :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                                    >
                                        {{ tag.name }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Initial Note -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Initial Note (Optional)</h3>
                            <textarea
                                v-model="form.note"
                                rows="4"
                                placeholder="Add a note about this contact..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            />
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3">
                            <Link
                                :href="route('contacts.index')"
                                class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Contact</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
