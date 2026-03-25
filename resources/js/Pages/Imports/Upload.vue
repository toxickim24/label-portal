<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const fileInput = ref(null);
const form = useForm({
    file: null,
});

const handleFileChange = (event) => {
    form.file = event.target.files[0];
};

const submit = () => {
    form.post(route('imports.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const downloadTemplate = () => {
    window.location.href = route('imports.template');
};
</script>

<template>
    <Head title="Import Contacts" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Import Contacts from CSV
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                Upload CSV File
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Upload a CSV file containing contact information. Maximum file size: 10MB
                            </p>
                        </div>

                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">
                                Before you start:
                            </h4>
                            <ul class="list-disc list-inside text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <li>Download our CSV template to ensure proper formatting</li>
                                <li>Make sure your file has headers in the first row</li>
                                <li>Required fields: First Name, Last Name</li>
                                <li>Optional fields: Email, Phone, Address, City, State, Zip, Type, Status, Source, Priority</li>
                            </ul>
                            <button
                                @click="downloadTemplate"
                                type="button"
                                class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                            >
                                Download Template
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select CSV File
                                </label>
                                <input
                                    ref="fileInput"
                                    type="file"
                                    @change="handleFileChange"
                                    accept=".csv,.txt"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                />
                                <p v-if="form.errors.file" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.file }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <a
                                    :href="route('imports.index')"
                                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                                >
                                    View Import History
                                </a>
                                <button
                                    type="submit"
                                    :disabled="!form.file || form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Uploading...</span>
                                    <span v-else>Upload &amp; Continue</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
