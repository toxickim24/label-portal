<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// Logo paths (can be overridden from settings in the future)
const lightLogo = computed(() => '/images/logos/mcmullen-logo-blue.png');
const darkLogo = computed(() => '/images/logos/mcmullen-logo-white.png');
</script>

<template>
    <div class="flex items-center">
        <!-- Light mode logo -->
        <img
            :src="lightLogo"
            alt="McMullen Properties Logo"
            class="block h-full w-auto dark:hidden"
        />

        <!-- Dark mode logo -->
        <img
            :src="darkLogo"
            alt="McMullen Properties Logo"
            class="hidden h-full w-auto dark:block"
        />
    </div>
</template>
