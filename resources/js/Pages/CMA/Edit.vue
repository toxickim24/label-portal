<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    report: Object,
    contacts: Array,
    comparisonData: Object,
});

// Comparable form
const showComparableForm = ref(false);
const editingComparableIndex = ref(null);
const comparableForm = useForm({
    address: '',
    square_feet: '',
    bedrooms: '',
    bathrooms: '',
    sale_price: '',
    sale_date: '',
    distance: '',
});

const resetComparableForm = () => {
    comparableForm.reset();
    showComparableForm.value = false;
    editingComparableIndex.value = null;
};

const saveComparable = () => {
    if (editingComparableIndex.value !== null) {
        // Update existing
        comparableForm.put(route('cma.comparable.update', [props.report.id, editingComparableIndex.value]), {
            onSuccess: () => resetComparableForm(),
        });
    } else {
        // Add new
        comparableForm.post(route('cma.comparable.add', props.report.id), {
            onSuccess: () => resetComparableForm(),
        });
    }
};

const removeComparable = (index) => {
    if (confirm('Remove this comparable property?')) {
        router.delete(route('cma.comparable.remove', [props.report.id, index]));
    }
};

const finalize = () => {
    if (confirm('Finalize this report? You won\'t be able to make further changes.')) {
        router.post(route('cma.finalize', props.report.id));
    }
};

const generatePdf = () => {
    router.post(route('cma.generate-pdf', props.report.id));
};

const downloadPdf = () => {
    window.open(route('cma.download-pdf', props.report.id), '_blank');
};

const emailPdf = () => {
    if (confirm('Send this report to the client via email?')) {
        router.post(route('cma.email-pdf', props.report.id));
    }
};

const comparables = computed(() => props.report.comparables || []);
</script>

<template>
    <Head :title="`Edit CMA: ${report.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ report.title }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Status:
                        <span :class="report.status === 'finalized' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'">
                            {{ report.status === 'finalized' ? 'Finalized' : 'Draft' }}
                        </span>
                    </p>
                </div>
                <Link
                    :href="route('cma.index')"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                >
                    ← Back to Reports
                </Link>
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
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Report Actions
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <button
                                v-if="report.status === 'draft'"
                                @click="finalize"
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Finalize Report
                            </button>
                            <button
                                @click="generatePdf"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Generate PDF
                            </button>
                            <button
                                v-if="report.generated_pdf_path"
                                @click="downloadPdf"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Download PDF
                            </button>
                            <button
                                v-if="report.contact && report.generated_pdf_path"
                                @click="emailPdf"
                                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
                            >
                                Email to Client
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Subject Property -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Subject Property
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold">Address:</span>
                                {{ report.subject_property?.address || 'N/A' }}
                            </div>
                            <div>
                                <span class="font-semibold">Square Feet:</span>
                                {{ report.subject_property?.square_feet ? Number(report.subject_property.square_feet).toLocaleString() : 'N/A' }}
                            </div>
                            <div>
                                <span class="font-semibold">Bedrooms:</span>
                                {{ report.subject_property?.bedrooms || 'N/A' }}
                            </div>
                            <div>
                                <span class="font-semibold">Bathrooms:</span>
                                {{ report.subject_property?.bathrooms || 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comparable Properties -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Comparable Properties ({{ comparables.length }})
                            </h3>
                            <button
                                v-if="report.status === 'draft' && !showComparableForm"
                                @click="showComparableForm = true"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Add Comparable
                            </button>
                        </div>

                        <!-- Add/Edit Comparable Form -->
                        <div v-if="showComparableForm" class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <h4 class="font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                {{ editingComparableIndex !== null ? 'Edit' : 'Add' }} Comparable Property
                            </h4>
                            <form @submit.prevent="saveComparable" class="space-y-4">
                                <div>
                                    <InputLabel value="Address *" />
                                    <TextInput
                                        v-model="comparableForm.address"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel value="Square Feet *" />
                                        <TextInput
                                            v-model="comparableForm.square_feet"
                                            type="number"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Sale Price *" />
                                        <TextInput
                                            v-model="comparableForm.sale_price"
                                            type="number"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel value="Bedrooms" />
                                        <TextInput
                                            v-model="comparableForm.bedrooms"
                                            type="number"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Bathrooms" />
                                        <TextInput
                                            v-model="comparableForm.bathrooms"
                                            type="number"
                                            step="0.5"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Sale Date" />
                                        <TextInput
                                            v-model="comparableForm.sale_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <PrimaryButton :disabled="comparableForm.processing">
                                        Save Comparable
                                    </PrimaryButton>
                                    <button
                                        type="button"
                                        @click="resetComparableForm"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-400 dark:hover:bg-gray-600"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Comparables List -->
                        <div v-if="comparables.length > 0" class="space-y-4">
                            <div
                                v-for="(comp, index) in comparables"
                                :key="index"
                                class="border dark:border-gray-700 rounded-lg p-4"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ comp.address }}
                                        </h4>
                                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div><strong>Sale Price:</strong> ${{ Number(comp.sale_price).toLocaleString() }}</div>
                                            <div><strong>Sq Ft:</strong> {{ Number(comp.square_feet).toLocaleString() }}</div>
                                            <div><strong>Beds:</strong> {{ comp.bedrooms || 'N/A' }}</div>
                                            <div><strong>Baths:</strong> {{ comp.bathrooms || 'N/A' }}</div>
                                        </div>
                                        <div v-if="comp.sale_date" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            <strong>Sale Date:</strong> {{ new Date(comp.sale_date).toLocaleDateString() }}
                                        </div>
                                    </div>
                                    <button
                                        v-if="report.status === 'draft'"
                                        @click="removeComparable(index)"
                                        class="ml-4 px-3 py-1 text-sm bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded hover:bg-red-200 dark:hover:bg-red-800"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No comparable properties added yet. Add at least 3 comparables for accurate valuation.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
