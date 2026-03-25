<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    email: String,
    name: String,
});

const form = useForm({
    token: props.token,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('client.invitation.process'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Accept Invitation" />

        <div class="mb-4 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Welcome, {{ name }}!
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                You've been invited to join McMullen Properties Client Portal.
                Please set your password to activate your account.
            </p>
        </div>

        <div class="mb-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold">Email:</span> {{ email }}
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Must be at least 8 characters long.
                </p>
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="form.errors.error" class="mt-4">
                <div class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                    <p class="text-sm text-red-800 dark:text-red-200">
                        {{ form.errors.error }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Activate Account
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
