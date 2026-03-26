<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    report: Object,
    comparisonData: Object,
});

const downloadPdf = (reportId) => {
    window.open(route('cma.download-pdf', reportId), '_blank');
};
</script>

<template>
    <Head :title="`CMA: ${report.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ report.title }}
                </h2>
                <div class="flex gap-3">
                    <Link
                        v-if="report.status === 'draft'"
                        :href="route('cma.edit', report.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                        Edit Report
                    </Link>
                    <Link
                        :href="route('cma.index')"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                    >
                        ← Back to Reports
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Valuation Summary -->
                <div v-if="report.valuation_avg" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Estimated Market Value
                        </h3>
                        <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                            ${{ Number(report.valuation_avg).toLocaleString() }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Range: ${{ Number(report.valuation_low).toLocaleString() }} - ${{ Number(report.valuation_high).toLocaleString() }}
                        </div>
                        <button
                            v-if="report.generated_pdf_path"
                            @click="downloadPdf(report.id)"
                            class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                        >
                            Download PDF Report
                        </button>
                    </div>
                </div>

                <!-- Subject Property -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Subject Property
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="text-sm">
                                <span class="font-semibold">Address:</span>
                                {{ report.subject_property?.address || 'N/A' }}
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold">Square Feet:</span>
                                {{ report.subject_property?.square_feet ? Number(report.subject_property.square_feet).toLocaleString() : 'N/A' }}
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold">Bedrooms:</span>
                                {{ report.subject_property?.bedrooms || 'N/A' }}
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold">Bathrooms:</span>
                                {{ report.subject_property?.bathrooms || 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comparable Properties -->
                <div v-if="comparisonData && comparisonData.comparables && comparisonData.comparables.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Comparable Properties ({{ comparisonData.comparables.length }})
                        </h3>
                        <div class="space-y-4">
                            <div
                                v-for="(comp, index) in comparisonData.comparables"
                                :key="index"
                                class="border dark:border-gray-700 rounded-lg p-4"
                            >
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ comp.data.address }}
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div><strong>Sale Price:</strong> ${{ Number(comp.data.sale_price).toLocaleString() }}</div>
                                    <div><strong>Adjusted:</strong> ${{ Number(comp.adjusted_price).toLocaleString() }}</div>
                                    <div><strong>Sq Ft:</strong> {{ Number(comp.data.square_feet).toLocaleString() }}</div>
                                    <div><strong>$/Sq Ft:</strong> ${{ comp.price_per_sqft || 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
