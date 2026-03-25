<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const page = usePage();

const props = defineProps({
    contact: {
        type: Object,
        default: () => null
    }
});

const editingProfile = ref(false);
const editingPassword = ref(false);

// Profile form
const profileForm = useForm({
    name: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip: '',
});

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const startEditProfile = () => {
    profileForm.name = page.props.auth.user.name;
    profileForm.phone = props.contact?.phone || '';
    profileForm.address = props.contact?.address || '';
    profileForm.city = props.contact?.city || '';
    profileForm.state = props.contact?.state || '';
    profileForm.zip = props.contact?.zip || '';
    editingProfile.value = true;
};

const cancelEditProfile = () => {
    profileForm.reset();
    profileForm.clearErrors();
    editingProfile.value = false;
};

const submitProfile = () => {
    profileForm.patch(route('client.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            editingProfile.value = false;
        },
    });
};

const startEditPassword = () => {
    editingPassword.value = true;
};

const cancelEditPassword = () => {
    passwordForm.reset();
    passwordForm.clearErrors();
    editingPassword.value = false;
};

const submitPassword = () => {
    passwordForm.patch(route('client.profile.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            editingPassword.value = false;
        },
    });
};
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
                <!-- Success Message -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400"
                >
                    {{ $page.props.flash.success }}
                </div>

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
                                            {{ $page.props.auth.user.created_at ? new Date($page.props.auth.user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Email Verified
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            <span class="inline-flex items-center">
                                                <svg v-if="$page.props.auth.user.email_verified_at" class="mr-1.5 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $page.props.auth.user.email_verified_at ? 'Yes' : 'No' }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="lg:col-span-2">
                        <!-- Edit Profile Form -->
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            Personal Information
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Update your personal information
                                        </p>
                                    </div>
                                    <button
                                        v-if="!editingProfile"
                                        @click="startEditProfile"
                                        type="button"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                    >
                                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Profile
                                    </button>
                                </div>

                                <form v-if="editingProfile" @submit.prevent="submitProfile" class="mt-6">
                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel for="name" value="Full Name" />
                                            <TextInput
                                                id="name"
                                                v-model="profileForm.name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <InputError :message="profileForm.errors.name" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="phone" value="Phone Number" />
                                            <TextInput
                                                id="phone"
                                                v-model="profileForm.phone"
                                                type="tel"
                                                class="mt-1 block w-full"
                                                placeholder="(555) 123-4567"
                                            />
                                            <InputError :message="profileForm.errors.phone" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="address" value="Street Address" />
                                            <TextInput
                                                id="address"
                                                v-model="profileForm.address"
                                                type="text"
                                                class="mt-1 block w-full"
                                                placeholder="123 Main St"
                                            />
                                            <InputError :message="profileForm.errors.address" class="mt-2" />
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div>
                                                <InputLabel for="city" value="City" />
                                                <TextInput
                                                    id="city"
                                                    v-model="profileForm.city"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="City"
                                                />
                                                <InputError :message="profileForm.errors.city" class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel for="state" value="State" />
                                                <TextInput
                                                    id="state"
                                                    v-model="profileForm.state"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="State"
                                                />
                                                <InputError :message="profileForm.errors.state" class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel for="zip" value="ZIP Code" />
                                                <TextInput
                                                    id="zip"
                                                    v-model="profileForm.zip"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="12345"
                                                />
                                                <InputError :message="profileForm.errors.zip" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <PrimaryButton :disabled="profileForm.processing">
                                            Save Changes
                                        </PrimaryButton>
                                        <button
                                            type="button"
                                            @click="cancelEditProfile"
                                            class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>

                                <div v-else class="mt-6 border-t border-gray-200 dark:border-gray-700">
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
                                        <div v-if="contact && contact.tags && contact.tags.length > 0" class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Tags
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 dark:text-white">
                                                <div class="flex flex-wrap gap-2">
                                                    <span
                                                        v-for="tag in contact.tags"
                                                        :key="tag.id"
                                                        :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                                                        :class="[`ring-[${tag.color}]`]"
                                                    >
                                                        {{ tag.name }}
                                                    </span>
                                                </div>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Agent Notes -->
                        <div v-if="contact && contact.notes && contact.notes.length > 0" class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Notes from Your Agent
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Important information shared by your agent
                                </p>
                                <div class="mt-6 space-y-4">
                                    <div
                                        v-for="note in contact.notes"
                                        :key="note.id"
                                        class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                        :class="{ 'border-l-4 border-l-indigo-500': note.is_pinned }"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span v-if="note.is_pinned" class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                                    </svg>
                                                    Important
                                                </span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ new Date(note.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                                </span>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-wrap dark:text-gray-300">
                                            {{ note.content }}
                                        </p>
                                        <div v-if="note.user" class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ note.user.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            Change Password
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Update your account password
                                        </p>
                                    </div>
                                    <button
                                        v-if="!editingPassword"
                                        @click="startEditPassword"
                                        type="button"
                                        class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                                    >
                                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        Change Password
                                    </button>
                                </div>

                                <form v-if="editingPassword" @submit.prevent="submitPassword" class="mt-6">
                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel for="current_password" value="Current Password" />
                                            <TextInput
                                                id="current_password"
                                                v-model="passwordForm.current_password"
                                                type="password"
                                                class="mt-1 block w-full"
                                                autocomplete="current-password"
                                                required
                                            />
                                            <InputError :message="passwordForm.errors.current_password" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="password" value="New Password" />
                                            <TextInput
                                                id="password"
                                                v-model="passwordForm.password"
                                                type="password"
                                                class="mt-1 block w-full"
                                                autocomplete="new-password"
                                                required
                                            />
                                            <InputError :message="passwordForm.errors.password" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="password_confirmation" value="Confirm New Password" />
                                            <TextInput
                                                id="password_confirmation"
                                                v-model="passwordForm.password_confirmation"
                                                type="password"
                                                class="mt-1 block w-full"
                                                autocomplete="new-password"
                                                required
                                            />
                                            <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <PrimaryButton :disabled="passwordForm.processing">
                                            Update Password
                                        </PrimaryButton>
                                        <button
                                            type="button"
                                            @click="cancelEditPassword"
                                            class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
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
                                            Need Assistance?
                                        </h3>
                                        <p class="mt-1 text-sm text-indigo-100">
                                            Contact your assigned agent for questions about your account, properties, or services.
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
                                                href="mailto:tim@mcmullen.properties"
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
