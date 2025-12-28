import {ref} from 'vue';

const isDark = ref(false);

// Initialize theme immediately on module load
if (typeof window !== 'undefined') {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
    isDark.value = shouldBeDark;
}

export function useTheme() {
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

        isDark.value = shouldBeDark;

        if (shouldBeDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    const toggleTheme = () => {
        isDark.value = !isDark.value;
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    };

    // Watch for system theme changes
    if (typeof window !== 'undefined') {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = (e) => {
            if (!localStorage.getItem('theme')) {
                isDark.value = e.matches;
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        };
        mediaQuery.addEventListener('change', handleChange);

        // Watch for theme changes from other tabs/windows
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                const newTheme = e.newValue;
                isDark.value = newTheme === 'dark';
                if (newTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });

        // Periodically check if theme class is still applied (defensive)
        setInterval(() => {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
            const hasDarkClass = document.documentElement.classList.contains('dark');

            if (shouldBeDark !== hasDarkClass) {
                if (shouldBeDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                isDark.value = shouldBeDark;
            }
        }, 1000);
    }

    return {
        isDark,
        initTheme,
        toggleTheme
    };
}

