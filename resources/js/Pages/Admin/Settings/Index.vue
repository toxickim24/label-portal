<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
    logoUrl: String,
    faviconUrl: String,
});

const form = useForm({
    app_name: props.settings.app_name || '',
    logo: null,
    favicon: null,
});

const logoPreview = ref(null);
const faviconPreview = ref(null);

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleFaviconChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.favicon = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            faviconPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.settings.branding'), {
        forceFormData: true,
        onSuccess: () => {
            logoPreview.value = null;
            faviconPreview.value = null;
        },
    });
};

const resetLogos = () => {
    if (confirm('Are you sure you want to reset logos to defaults? This will remove any custom logos you have uploaded.')) {
        router.post(route('admin.settings.reset-logos'));
    }
};
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Branding Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Application Name -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Application Name
                        </h3>

                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="app_name" value="Application Name" />
                                <TextInput
                                    id="app_name"
                                    v-model="form.app_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Label Client Portal"
                                />
                                <InputError class="mt-2" :message="form.errors.app_name" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    This name will appear in the browser title and throughout the application.
                                </p>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Save Application Name
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Application Logo
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Current Logo -->
                            <div>
                                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Current Logo (Light Mode)
                                </p>
                                <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                                    <img
                                        v-if="logoPreview"
                                        :src="logoPreview"
                                        alt="Logo Preview"
                                        class="mx-auto h-20 w-auto"
                                    />
                                    <img
                                        v-else
                                        :src="logoUrl"
                                        alt="Current Logo"
                                        class="mx-auto h-20 w-auto"
                                    />
                                </div>
                            </div>

                            <!-- Upload New Logo -->
                            <div>
                                <InputLabel for="logo" value="Upload New Logo" />
                                <input
                                    id="logo"
                                    type="file"
                                    accept="image/*"
                                    @change="handleLogoChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500 dark:text-gray-400"
                                />
                                <InputError class="mt-2" :message="form.errors.logo" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    PNG, JPG, or GIF (MAX. 2MB)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Favicon Upload -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Favicon
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Current Favicon -->
                            <div>
                                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Current Favicon
                                </p>
                                <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                                    <img
                                        v-if="faviconPreview"
                                        :src="faviconPreview"
                                        alt="Favicon Preview"
                                        class="mx-auto h-16 w-16"
                                    />
                                    <img
                                        v-else
                                        :src="faviconUrl"
                                        alt="Current Favicon"
                                        class="mx-auto h-16 w-16"
                                    />
                                </div>
                            </div>

                            <!-- Upload New Favicon -->
                            <div>
                                <InputLabel for="favicon" value="Upload New Favicon" />
                                <input
                                    id="favicon"
                                    type="file"
                                    accept="image/*"
                                    @change="handleFaviconChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500 dark:text-gray-400"
                                />
                                <InputError class="mt-2" :message="form.errors.favicon" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    PNG or ICO (MAX. 1MB, recommended 32x32px)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <button
                                    @click="resetLogos"
                                    type="button"
                                    class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600"
                                >
                                    Reset to Default Logos
                                </button>
                            </div>
                            <div>
                                <PrimaryButton
                                    @click="submit"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing || (!form.logo && !form.favicon && !form.isDirty)"
                                >
                                    Save Branding Changes
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
