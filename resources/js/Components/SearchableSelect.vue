<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    options: Array,
    placeholder: {
        type: String,
        default: 'Search...',
    },
    label: {
        type: String,
        default: 'label',
    },
    valueKey: {
        type: String,
        default: 'value',
    },
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const isOpen = ref(false);

const filteredOptions = computed(() => {
    if (!searchQuery.value) {
        return props.options;
    }

    const query = searchQuery.value.toLowerCase();
    return props.options.filter(option => {
        const labelValue = typeof option === 'object' ? option[props.label] : option;
        return labelValue.toLowerCase().includes(query);
    });
});

const selectedOption = computed(() => {
    if (!props.modelValue) return null;
    return props.options.find(option => {
        const value = typeof option === 'object' ? option[props.valueKey] : option;
        return value === props.modelValue;
    });
});

const selectedLabel = computed(() => {
    if (!selectedOption.value) return '';
    return typeof selectedOption.value === 'object'
        ? selectedOption.value[props.label]
        : selectedOption.value;
});

const selectOption = (option) => {
    const value = typeof option === 'object' ? option[props.valueKey] : option;
    emit('update:modelValue', value);
    isOpen.value = false;
    searchQuery.value = '';
};

const clearSelection = () => {
    emit('update:modelValue', null);
    searchQuery.value = '';
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        setTimeout(() => {
            document.getElementById('search-input')?.focus();
        }, 100);
    }
};
</script>

<template>
    <div class="relative">
        <!-- Selected Value Display -->
        <button
            type="button"
            @click="toggleDropdown"
            class="w-full flex items-center justify-between px-3 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300"
        >
            <span v-if="selectedLabel" class="block truncate">
                {{ selectedLabel }}
            </span>
            <span v-else class="block truncate text-gray-400 dark:text-gray-500">
                {{ placeholder }}
            </span>
            <svg
                class="h-5 w-5 text-gray-400 ml-2"
                :class="{ 'transform rotate-180': isOpen }"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>

        <!-- Dropdown Panel -->
        <div
            v-if="isOpen"
            class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
            <!-- Search Input -->
            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-2 py-2 border-b border-gray-200 dark:border-gray-700">
                <input
                    id="search-input"
                    v-model="searchQuery"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300"
                    :placeholder="placeholder"
                    @click.stop
                />
            </div>

            <!-- Clear Button -->
            <button
                v-if="selectedOption"
                type="button"
                @click="clearSelection"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700"
            >
                <span class="italic text-gray-500 dark:text-gray-400">Clear selection</span>
            </button>

            <!-- Options List -->
            <div v-if="filteredOptions.length > 0">
                <button
                    v-for="option in filteredOptions"
                    :key="typeof option === 'object' ? option[valueKey] : option"
                    type="button"
                    @click="selectOption(option)"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-indigo-900"
                    :class="{
                        'bg-indigo-50 dark:bg-indigo-900': modelValue === (typeof option === 'object' ? option[valueKey] : option),
                    }"
                >
                    {{ typeof option === 'object' ? option[label] : option }}
                </button>
            </div>

            <!-- No Results -->
            <div v-else class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 italic">
                No results found
            </div>
        </div>

        <!-- Backdrop -->
        <div
            v-if="isOpen"
            @click="isOpen = false"
            class="fixed inset-0 z-0"
        ></div>
    </div>
</template>
