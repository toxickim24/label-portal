<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    importRecord: Object,
    statistics: Object,
    errors: Object,
});

const refreshInterval = ref(null);

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

// Auto-refresh when status is pending or processing
onMounted(() => {
    if (props.importRecord.status === 'pending' || props.importRecord.status === 'processing') {
        refreshInterval.value = setInterval(() => {
            router.reload({ only: ['importRecord', 'statistics', 'errors'], preserveScroll: true });
        }, 2000); // Refresh every 2 seconds
    }
});

// Clean up interval when component unmounts or status changes
onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

// Stop auto-refresh when completed or failed
if (props.importRecord.status === 'completed' || props.importRecord.status === 'failed') {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
        refreshInterval.value = null;
    }
}
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
                <!-- Pending/Processing Alert -->
                <div v-if="importRecord.status === 'pending' || importRecord.status === 'processing'" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">
                                {{ importRecord.status === 'pending' ? 'Import Queued' : 'Processing Import' }}
                            </h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                {{ importRecord.status === 'pending' ? 'Your import is waiting in the queue. This page will update automatically.' : 'Your import is being processed. This page will update automatically every 2 seconds.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Success Alert -->
                <div v-if="importRecord.status === 'completed'" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-green-900 dark:text-green-100">
                                Import Completed Successfully!
                            </h3>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                {{ statistics.successful_rows }} contact(s) imported successfully
                                <span v-if="statistics.skipped_rows > 0">, {{ statistics.skipped_rows }} skipped</span>
                                <span v-if="statistics.failed_rows > 0">, {{ statistics.failed_rows }} failed</span>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Failed Alert -->
                <div v-if="importRecord.status === 'failed'" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-red-900 dark:text-red-100">
                                Import Failed
                            </h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                The import could not be completed. Please check the errors below.
                            </p>
                        </div>
                    </div>
                </div>

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
