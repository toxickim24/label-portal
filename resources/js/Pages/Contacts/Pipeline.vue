<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

const props = defineProps({
    pipeline: Object,
    users: Array,
});

const draggingContact = ref(null);
const localPipeline = reactive({ ...props.pipeline });

const stages = [
    { key: 'lead', name: 'Lead', color: 'blue' },
    { key: 'contacted', name: 'Contacted', color: 'indigo' },
    { key: 'qualified', name: 'Qualified', color: 'purple' },
    { key: 'showing', name: 'Showing', color: 'pink' },
    { key: 'offer', name: 'Offer', color: 'yellow' },
    { key: 'contract', name: 'Contract', color: 'orange' },
    { key: 'closed', name: 'Closed', color: 'green' },
    { key: 'lost', name: 'Lost', color: 'gray' },
];

const dragStart = (contact) => {
    draggingContact.value = contact;
};

const dragOver = (event) => {
    event.preventDefault();
};

const drop = (newStatus) => {
    if (!draggingContact.value || draggingContact.value.status === newStatus) {
        draggingContact.value = null;
        return;
    }

    const contact = draggingContact.value;
    const oldStatus = contact.status;

    // Optimistically update UI - remove from old column
    const oldIndex = localPipeline[oldStatus].findIndex(c => c.id === contact.id);
    if (oldIndex > -1) {
        localPipeline[oldStatus].splice(oldIndex, 1);
    }

    // Add to new column
    const updatedContact = { ...contact, status: newStatus };
    localPipeline[newStatus].push(updatedContact);

    // Make API call in background
    router.post(
        route('contacts.status', contact.id),
        { status: newStatus },
        {
            preserveState: true,
            preserveScroll: true,
            onError: () => {
                // Revert on error
                const newIndex = localPipeline[newStatus].findIndex(c => c.id === contact.id);
                if (newIndex > -1) {
                    localPipeline[newStatus].splice(newIndex, 1);
                }
                localPipeline[oldStatus].push(contact);
            }
        }
    );

    draggingContact.value = null;
};

const getColorClasses = (color) => {
    const classes = {
        blue: 'bg-blue-50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-900',
        indigo: 'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/10 dark:border-indigo-900',
        purple: 'bg-purple-50 border-purple-200 dark:bg-purple-900/10 dark:border-purple-900',
        pink: 'bg-pink-50 border-pink-200 dark:bg-pink-900/10 dark:border-pink-900',
        yellow: 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/10 dark:border-yellow-900',
        orange: 'bg-orange-50 border-orange-200 dark:bg-orange-900/10 dark:border-orange-900',
        green: 'bg-green-50 border-green-200 dark:bg-green-900/10 dark:border-green-900',
        gray: 'bg-gray-50 border-gray-200 dark:bg-gray-900/10 dark:border-gray-700',
    };
    return classes[color] || classes.gray;
};

const getPriorityColor = (priority) => {
    const colors = {
        high: 'text-red-600 dark:text-red-400',
        medium: 'text-yellow-600 dark:text-yellow-400',
        low: 'text-gray-600 dark:text-gray-400',
    };
    return colors[priority] || colors.low;
};
</script>

<template>
    <Head title="Pipeline View" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Pipeline View
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('contacts.index')"
                        class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                    >
                        List View
                    </Link>
                    <Link
                        :href="route('contacts.create')"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Add Contact
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="mb-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        Drag and drop contacts between columns to update their status.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8">
                    <div
                        v-for="stage in stages"
                        :key="stage.key"
                        class="flex min-h-[500px] flex-col rounded-lg border-2 p-4"
                        :class="getColorClasses(stage.color)"
                        @dragover="dragOver"
                        @drop="drop(stage.key)"
                    >
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ stage.name }}
                            </h3>
                            <span class="rounded-full bg-white px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ localPipeline[stage.key]?.length || 0 }}
                            </span>
                        </div>

                        <div class="space-y-2 overflow-y-auto">
                            <div
                                v-for="contact in localPipeline[stage.key]"
                                :key="contact.id"
                                draggable="true"
                                @dragstart="dragStart(contact)"
                                class="cursor-move rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition hover:shadow-md dark:border-gray-600 dark:bg-gray-700"
                            >
                                <div class="mb-2 flex items-start justify-between">
                                    <div class="flex-1">
                                        <Link
                                            :href="route('contacts.show', contact.id)"
                                            class="text-sm font-medium text-gray-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400"
                                        >
                                            {{ contact.first_name }} {{ contact.last_name }}
                                        </Link>
                                        <div v-if="contact.contact_type" class="mt-1 text-xs capitalize text-gray-500 dark:text-gray-400">
                                            {{ contact.contact_type }}
                                        </div>
                                    </div>
                                    <div :class="getPriorityColor(contact.priority)">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                <div v-if="contact.email" class="mb-1 text-xs text-gray-600 dark:text-gray-400">
                                    {{ contact.email }}
                                </div>

                                <div v-if="contact.phone" class="mb-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ contact.phone }}
                                </div>

                                <div v-if="contact.assigned_user" class="mb-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ contact.assigned_user.name }}
                                </div>

                                <div v-if="contact.tags.length > 0" class="flex flex-wrap gap-1">
                                    <span
                                        v-for="tag in contact.tags.slice(0, 2)"
                                        :key="tag.id"
                                        class="rounded-full px-2 py-0.5 text-xs"
                                        :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                                    >
                                        {{ tag.name }}
                                    </span>
                                    <span v-if="contact.tags.length > 2" class="text-xs text-gray-500 dark:text-gray-400">
                                        +{{ contact.tags.length - 2 }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="!localPipeline[stage.key] || localPipeline[stage.key].length === 0" class="py-8 text-center text-xs text-gray-400 dark:text-gray-500">
                                No contacts
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
