<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    reports: Object,
});

const deleteReport = (report) => {
    if (confirm(`Are you sure you want to archive "${report.title}"?`)) {
        router.delete(route('cma.destroy', report.id));
    }
};

const duplicateReport = (report) => {
    router.post(route('cma.duplicate', report.id));
};

const downloadPdf = (report) => {
    window.open(route('cma.download-pdf', report.id), '_blank');
};
</script>

<template>
    <Head title="CMA Reports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    CMA Reports
                </h2>
                <Link
                    :href="route('cma.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Create New CMA
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Reports List -->
                        <div v-if="reports.data && reports.data.length > 0" class="space-y-4">
                            <div
                                v-for="report in reports.data"
                                :key="report.id"
                                class="border dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ report.title }}
                                            </h3>
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs font-medium rounded-full',
                                                    report.status === 'finalized'
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                ]"
                                            >
                                                {{ report.status === 'finalized' ? 'Finalized' : 'Draft' }}
                                            </span>
                                        </div>

                                        <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                            <p v-if="report.subject_address">
                                                <strong>Property:</strong> {{ report.subject_address }}
                                            </p>
                                            <p v-if="report.contact">
                                                <strong>Client:</strong> {{ report.contact.first_name }} {{ report.contact.last_name }}
                                            </p>
                                            <p v-if="report.valuation_avg">
                                                <strong>Estimated Value:</strong>
                                                ${{ Number(report.valuation_avg).toLocaleString() }}
                                            </p>
                                            <p>
                                                <strong>Created:</strong>
                                                {{ new Date(report.created_at).toLocaleDateString() }}
                                                by {{ report.user.name }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 ml-4">
                                        <Link
                                            :href="route('cma.show', report.id)"
                                            class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            v-if="report.status === 'draft'"
                                            :href="route('cma.edit', report.id)"
                                            class="px-3 py-1 text-sm bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded hover:bg-indigo-200 dark:hover:bg-indigo-800"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="report.generated_pdf_path"
                                            @click="downloadPdf(report)"
                                            class="px-3 py-1 text-sm bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded hover:bg-green-200 dark:hover:bg-green-800"
                                        >
                                            PDF
                                        </button>
                                        <button
                                            @click="duplicateReport(report)"
                                            class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-800"
                                        >
                                            Duplicate
                                        </button>
                                        <button
                                            @click="deleteReport(report)"
                                            class="px-3 py-1 text-sm bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded hover:bg-red-200 dark:hover:bg-red-800"
                                        >
                                            Archive
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                No CMA reports
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Get started by creating a new Comparative Market Analysis report.
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('cma.create')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    Create Your First CMA
                                </Link>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="reports.data && reports.data.length > 0" class="mt-6">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing {{ reports.from }} to {{ reports.to }} of {{ reports.total }} results
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-if="reports.prev_page_url"
                                        :href="reports.prev_page_url"
                                        class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="reports.next_page_url"
                                        :href="reports.next_page_url"
                                        class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                                    >
                                        Next
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
