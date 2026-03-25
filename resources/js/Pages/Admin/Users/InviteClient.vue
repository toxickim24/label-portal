<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post(route('admin.users.send-invitation'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Invite Client" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Invite Client
                </h2>
                <Link
                    :href="route('admin.users.index')"
                    class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                >
                    Back to Users
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Client Information
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Enter the client's information below. They will receive an email invitation to set their password and activate their account.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Full Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                    placeholder="John Doe"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email Address" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="john@example.com"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    The invitation will be sent to this email address.
                                </p>
                            </div>

                            <div v-if="form.errors.error" class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    {{ form.errors.error }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('admin.users.index')"
                                    class="rounded-md px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Send Invitation
                                </PrimaryButton>
                            </div>
                        </form>

                        <div class="mt-8 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        How Client Invitations Work
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="list-disc space-y-1 pl-5">
                                            <li>Client will receive an email with an activation link</li>
                                            <li>Link is valid for 7 days</li>
                                            <li>Client sets their own password upon activation</li>
                                            <li>Account is automatically approved and ready to use</li>
                                            <li>You can resend invitations from the user list if needed</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
