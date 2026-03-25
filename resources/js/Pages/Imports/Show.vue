<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    importRecord: Object,
    statistics: Object,
    errors: Object,
});

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`Import #${importRecord.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Import #{{ importRecord.id }}: {{ importRecord.original_filename }}
                </h2>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', getStatusColor(importRecord.status)]">
                    {{ importRecord.status.toUpperCase() }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Rows</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ statistics.total_rows }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Successful</div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ statistics.successful_rows }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Failed</div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ statistics.failed_rows }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Skipped</div>
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ statistics.skipped_rows }}</div>
                    </div>
                </div>

                <!--Progress Bar -->
                <div v-if="importRecord.status === 'processing'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Processing... {{ statistics.progress_percentage }}%
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${statistics.progress_percentage}%`"></div>
                    </div>
                </div>

                <!-- Errors -->
                <div v-if="statistics.failed_rows > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Errors</h3>
                            <a
                                :href="route('imports.failed-rows', importRecord.id)"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900"
                            >
                                Download Failed Rows
                            </a>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="error in errors.data"
                                :key="error.id"
                                class="border border-red-200 dark:border-red-800 rounded-lg p-4 bg-red-50 dark:bg-red-900/20"
                            >
                                <div class="text-sm font-medium text-red-800 dark:text-red-300">
                                    Row {{ error.row_number }}: {{ error.error_message }}
                                </div>
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    Data: {{ JSON.stringify(error.row_data) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <a
                        :href="route('imports.index')"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                    >
                        Back to Import History
                    </a>
                    <a
                        v-if="importRecord.status === 'completed' || importRecord.status === 'failed'"
                        :href="route('contacts.index')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        View Contacts
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
