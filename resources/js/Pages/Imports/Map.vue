<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    importRecord: Object,
    preview: Object,
    availableFields: Object,
});

const form = useForm({
    mapping: props.preview.suggested_mapping || {},
    duplicate_strategy: 'skip',
});

const submit = () => {
    form.post(route('imports.process', props.importRecord.id));
};
</script>

<template>
    <Head title="Map Columns" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Map CSV Columns
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                File: {{ importRecord.original_filename }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Total rows to import: {{ preview.total_rows }}
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Map CSV Columns to Contact Fields
                                </h4>
                                <div class="space-y-3">
                                    <div
                                        v-for="header in preview.headers"
                                        :key="header"
                                        class="grid grid-cols-2 gap-4 items-center"
                                    >
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ header }}
                                        </div>
                                        <select
                                            v-model="form.mapping[header]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 sm:text-sm"
                                        >
                                            <option :value="null">-- Skip this column --</option>
                                            <option
                                                v-for="(label, field) in availableFields"
                                                :key="field"
                                                :value="field"
                                            >
                                                {{ label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Duplicate Handling
                                </h4>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.duplicate_strategy"
                                            type="radio"
                                            value="skip"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Skip:</strong> Keep existing contact, ignore import row
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.duplicate_strategy"
                                            type="radio"
                                            value="update"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Update:</strong> Overwrite existing with import data
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.duplicate_strategy"
                                            type="radio"
                                            value="merge"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Merge:</strong> Keep non-empty fields from both
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">
                                    Preview (First Row)
                                </h4>
                                <div v-if="preview.data.length > 0" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-xs">
                                    <pre class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ JSON.stringify(preview.data[0], null, 2) }}</pre>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <a
                                    :href="route('imports.index')"
                                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Processing...</span>
                                    <span v-else">Start Import</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
