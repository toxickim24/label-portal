<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    contact: Object,
});

const showNoteForm = ref(false);
const editingNote = ref(null);

const noteForm = useForm({
    content: '',
    is_pinned: false,
});

const editNoteForm = useForm({
    content: '',
    is_pinned: false,
});

const submitNote = () => {
    noteForm.post(route('contact-notes.store', props.contact.id), {
        onSuccess: () => {
            noteForm.reset();
            showNoteForm.value = false;
        },
    });
};

const startEditNote = (note) => {
    editingNote.value = note.id;
    editNoteForm.content = note.content;
    editNoteForm.is_pinned = note.is_pinned;
};

const updateNote = (noteId) => {
    editNoteForm.put(route('contact-notes.update', noteId), {
        onSuccess: () => {
            editNoteForm.reset();
            editingNote.value = null;
        },
    });
};

const deleteNote = (noteId) => {
    if (confirm('Are you sure you want to delete this note?')) {
        router.delete(route('contact-notes.destroy', noteId));
    }
};

const togglePin = (noteId) => {
    router.post(route('contact-notes.toggle-pin', noteId));
};

const getStatusBadgeClass = (status) => {
    const classes = {
        lead: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        prospect: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
        active: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        archived: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPriorityBadgeClass = (priority) => {
    const classes = {
        low: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        high: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };
    return classes[priority] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="`${contact.first_name} ${contact.last_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ contact.first_name }} {{ contact.last_name }}
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('contacts.edit', contact.id)"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Edit Contact
                    </Link>
                    <Link
                        :href="route('contacts.index')"
                        class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                    >
                        Back to List
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Contact Details -->
                    <div class="lg:col-span-2">
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact Information</h3>
                            </div>
                            <div class="p-6">
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.first_name }} {{ contact.last_name }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.email || 'N/A' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.phone || 'N/A' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Type</dt>
                                        <dd class="mt-1 text-sm capitalize text-gray-900 dark:text-white">{{ contact.contact_type || 'N/A' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="mt-1">
                                            <span :class="getStatusBadgeClass(contact.status)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                                {{ contact.status }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Priority</dt>
                                        <dd class="mt-1">
                                            <span :class="getPriorityBadgeClass(contact.priority)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                                {{ contact.priority }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Source</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.source || 'N/A' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned To</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.assigned_user?.name || 'Unassigned' }}</dd>
                                    </div>

                                    <div v-if="contact.address" class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ contact.address }}<br v-if="contact.city || contact.state || contact.zip">
                                            <span v-if="contact.city || contact.state || contact.zip">
                                                {{ contact.city }}{{ contact.city && contact.state ? ', ' : '' }}{{ contact.state }} {{ contact.zip }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div v-if="contact.tags.length > 0" class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
                                        <dd class="mt-2 flex flex-wrap gap-2">
                                            <span
                                                v-for="tag in contact.tags"
                                                :key="tag.id"
                                                class="rounded-full px-3 py-1 text-sm"
                                                :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                                            >
                                                {{ tag.name }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mt-6 overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notes</h3>
                                    <button
                                        @click="showNoteForm = !showNoteForm"
                                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                    >
                                        Add Note
                                    </button>
                                </div>
                            </div>
                            <div class="p-6">
                                <!-- Add Note Form -->
                                <div v-if="showNoteForm" class="mb-6">
                                    <form @submit.prevent="submitNote">
                                        <textarea
                                            v-model="noteForm.content"
                                            rows="3"
                                            placeholder="Add a note..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            required
                                        />
                                        <div class="mt-3 flex items-center justify-between">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="noteForm.is_pinned"
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pin this note</span>
                                            </label>
                                            <div class="flex gap-2">
                                                <button
                                                    type="button"
                                                    @click="showNoteForm = false"
                                                    class="rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                                                >
                                                    Cancel
                                                </button>
                                                <button
                                                    type="submit"
                                                    :disabled="noteForm.processing"
                                                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
                                                >
                                                    Save Note
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Notes List -->
                                <div class="space-y-4">
                                    <div
                                        v-for="note in contact.notes"
                                        :key="note.id"
                                        class="rounded-lg border p-4 dark:border-gray-700"
                                        :class="note.is_pinned ? 'border-indigo-200 bg-indigo-50 dark:border-indigo-900 dark:bg-indigo-900/10' : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/10'"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div v-if="editingNote === note.id">
                                                    <form @submit.prevent="updateNote(note.id)">
                                                        <textarea
                                                            v-model="editNoteForm.content"
                                                            rows="3"
                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            required
                                                        />
                                                        <div class="mt-2 flex items-center justify-between">
                                                            <label class="inline-flex items-center">
                                                                <input
                                                                    type="checkbox"
                                                                    v-model="editNoteForm.is_pinned"
                                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                                />
                                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pin this note</span>
                                                            </label>
                                                            <div class="flex gap-2">
                                                                <button
                                                                    type="button"
                                                                    @click="editingNote = null"
                                                                    class="rounded-md bg-gray-600 px-3 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                                                                >
                                                                    Cancel
                                                                </button>
                                                                <button
                                                                    type="submit"
                                                                    class="rounded-md bg-indigo-600 px-3 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                                                >
                                                                    Update
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div v-else>
                                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ note.content }}</p>
                                                    <div class="mt-2 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                                        <span>{{ note.user.name }}</span>
                                                        <span>•</span>
                                                        <span>{{ formatDate(note.created_at) }}</span>
                                                        <span v-if="note.is_pinned" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400">
                                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                                                            </svg>
                                                            Pinned
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex gap-2">
                                                <button
                                                    @click="togglePin(note.id)"
                                                    class="text-gray-400 hover:text-gray-500"
                                                    :title="note.is_pinned ? 'Unpin note' : 'Pin note'"
                                                >
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="startEditNote(note)"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    @click="deleteNote(note.id)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="contact.notes.length === 0" class="text-center text-gray-500 dark:text-gray-400">
                                        No notes yet. Add one to get started.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Info</h3>
                            </div>
                            <div class="p-6">
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(contact.created_at) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(contact.updated_at) }}</dd>
                                    </div>
                                    <div v-if="contact.last_contact_date">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Contact</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(contact.last_contact_date) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.notes.length }} note(s)</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ contact.tags.length }} tag(s)</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
