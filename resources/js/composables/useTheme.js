import { ref, watch, onMounted } from 'vue';

const isDark = ref(false);

export function useTheme() {
    const initTheme = () => {
        // Check localStorage first, then system preference
        const savedTheme = localStorage.getItem('theme');

        if (savedTheme) {
            isDark.value = savedTheme === 'dark';
        } else {
            // Check system preference
            isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        applyTheme();
    };

    const applyTheme = () => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    const toggleTheme = () => {
        isDark.value = !isDark.value;
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
        applyTheme();
    };

    const setTheme = (theme) => {
        isDark.value = theme === 'dark';
        localStorage.setItem('theme', theme);
        applyTheme();
    };

    // Watch for changes and apply
    watch(isDark, applyTheme);

    return {
        isDark,
        toggleTheme,
        setTheme,
        initTheme,
    };
}
