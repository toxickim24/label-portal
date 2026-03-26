<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    contacts: Array,
    preselectedContactId: Number,
});

const contactOptions = computed(() => {
    return props.contacts.map(contact => ({
        value: contact.id,
        label: `${contact.name} (${contact.email})`,
    }));
});

const form = useForm({
    title: '',
    contact_id: props.preselectedContactId || null,
    subject_property: {
        address: '',
        square_feet: '',
        bedrooms: '',
        bathrooms: '',
        lot_size: '',
        year_built: '',
        property_type: 'single_family',
    },
    notes: '',
});

const submit = () => {
    form.post(route('cma.store'));
};
</script>

<template>
    <Head title="Create CMA Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Create CMA Report
                </h2>
                <Link
                    :href="route('cma.index')"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                >
                    ← Back to Reports
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Report Title -->
                        <div class="mb-6">
                            <InputLabel for="title" value="Report Title *" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., CMA for 123 Main Street"
                            />
                            <InputError :message="form.errors.title" class="mt-2" />
                        </div>

                        <!-- Contact Selection -->
                        <div class="mb-6">
                            <InputLabel for="contact_id" value="Client (Optional)" />
                            <SearchableSelect
                                v-model="form.contact_id"
                                :options="contactOptions"
                                placeholder="Search for a client..."
                                label="label"
                                value-key="value"
                                class="mt-1"
                            />
                            <InputError :message="form.errors.contact_id" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ contacts.length }} client(s) available
                            </p>
                        </div>

                        <!-- Subject Property Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                Subject Property
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="address" value="Property Address *" />
                                    <TextInput
                                        id="address"
                                        v-model="form.subject_property.address"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        placeholder="123 Main Street, City, ST 12345"
                                    />
                                    <InputError :message="form.errors['subject_property.address']" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="square_feet" value="Square Feet *" />
                                        <TextInput
                                            id="square_feet"
                                            v-model="form.subject_property.square_feet"
                                            type="number"
                                            class="mt-1 block w-full"
                                            required
                                            min="0"
                                            placeholder="2000"
                                        />
                                        <InputError :message="form.errors['subject_property.square_feet']" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="property_type" value="Property Type" />
                                        <select
                                            id="property_type"
                                            v-model="form.subject_property.property_type"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        >
                                            <option value="single_family">Single Family</option>
                                            <option value="condo">Condo</option>
                                            <option value="townhouse">Townhouse</option>
                                            <option value="multi_family">Multi-Family</option>
                                            <option value="land">Land</option>
                                        </select>
                                        <InputError :message="form.errors['subject_property.property_type']" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel for="bedrooms" value="Bedrooms" />
                                        <TextInput
                                            id="bedrooms"
                                            v-model="form.subject_property.bedrooms"
                                            type="number"
                                            class="mt-1 block w-full"
                                            min="0"
                                            placeholder="3"
                                        />
                                        <InputError :message="form.errors['subject_property.bedrooms']" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="bathrooms" value="Bathrooms" />
                                        <TextInput
                                            id="bathrooms"
                                            v-model="form.subject_property.bathrooms"
                                            type="number"
                                            step="0.5"
                                            class="mt-1 block w-full"
                                            min="0"
                                            placeholder="2.5"
                                        />
                                        <InputError :message="form.errors['subject_property.bathrooms']" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="year_built" value="Year Built" />
                                        <TextInput
                                            id="year_built"
                                            v-model="form.subject_property.year_built"
                                            type="number"
                                            class="mt-1 block w-full"
                                            min="1800"
                                            :max="new Date().getFullYear() + 1"
                                            placeholder="2010"
                                        />
                                        <InputError :message="form.errors['subject_property.year_built']" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="lot_size" value="Lot Size (sq ft)" />
                                    <TextInput
                                        id="lot_size"
                                        v-model="form.subject_property.lot_size"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        placeholder="8000"
                                    />
                                    <InputError :message="form.errors['subject_property.lot_size']" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <InputLabel for="notes" value="Notes (Optional)" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                rows="3"
                                placeholder="Additional notes about this CMA report..."
                            ></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('cma.index')"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                Create CMA Report
                            </PrimaryButton>
                        </div>

                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                            After creating the report, you'll be able to add comparable properties and generate the valuation.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
