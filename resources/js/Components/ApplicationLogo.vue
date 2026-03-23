<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// Logo paths (can be overridden from settings in the future)
const lightLogo = computed(() => '/images/logos/label-logo.png');
const darkLogo = computed(() => '/images/logos/label-white-logo.png');
</script>

<template>
    <!-- Light mode logo -->
    <img
        :src="lightLogo"
        alt="Label Portal Logo"
        class="block dark:hidden"
    />

    <!-- Dark mode logo -->
    <img
        :src="darkLogo"
        alt="Label Portal Logo"
        class="hidden dark:block"
    />
</template>
