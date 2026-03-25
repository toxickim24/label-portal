<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    contact: {
        type: Object,
        default: () => null
    }
});
</script>

<template>
    <Head title="My Profile" />

    <ClientLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                My Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Profile Card -->
                    <div class="lg:col-span-1">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <div class="text-center">
                                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/20">
                                        <svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ $page.props.auth.user.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Client
                                    </p>
                                    <div class="mt-4 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Active Account
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Info -->
                        <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Quick Info
                                </h4>
                                <dl class="mt-4 space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Member Since
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ new Date($page.props.auth.user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Email Verified
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $page.props.auth.user.email_verified_at ? 'Yes' : 'No' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="lg:col-span-2">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Personal Information
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    View your account information below. To update your profile, please contact your agent.
                                </p>

                                <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Full Name
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                {{ $page.props.auth.user.name }}
                                            </dd>
                                        </div>
                                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Email Address
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                {{ $page.props.auth.user.email }}
                                            </dd>
                                        </div>
                                        <div v-if="contact" class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Phone Number
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                {{ contact.phone || 'Not provided' }}
                                            </dd>
                                        </div>
                                        <div v-if="contact" class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Contact Type
                                            </dt>
                                            <dd class="mt-1 text-sm capitalize text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                {{ contact.contact_type || 'Not specified' }}
                                            </dd>
                                        </div>
                                        <div v-if="contact" class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Status
                                            </dt>
                                            <dd class="mt-1 text-sm capitalize text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                {{ contact.status || 'Active' }}
                                            </dd>
                                        </div>
                                        <div v-if="contact && contact.assigned_user" class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Assigned Agent
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                <div class="flex items-center">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                            {{ contact.assigned_user.name.split(' ').map(n => n[0]).join('') }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="font-medium">{{ contact.assigned_user.name }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ contact.assigned_user.email }}</p>
                                                    </div>
                                                </div>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Agent Card -->
                        <div class="mt-6 overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-600 shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-white">
                                            Need to Update Your Information?
                                        </h3>
                                        <p class="mt-1 text-sm text-indigo-100">
                                            Contact your assigned agent to update your profile details, preferences, or any other information.
                                        </p>
                                        <div class="mt-4">
                                            <a
                                                v-if="contact && contact.assigned_user"
                                                :href="`mailto:${contact.assigned_user.email}`"
                                                class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50"
                                            >
                                                Contact {{ contact.assigned_user.name.split(' ')[0] }}
                                            </a>
                                            <a
                                                v-else
                                                href="mailto:support@mcmullenproperties.com"
                                                class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50"
                                            >
                                                Contact Support
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
