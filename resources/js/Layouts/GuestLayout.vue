<script setup lang="ts">
import { onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Sun, Moon } from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';
import { useTheme } from '@/Composables/useTheme';

const { isDark, initTheme, toggleTheme } = useTheme();

onMounted(() => {
    initTheme();
});
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-background pt-6 sm:justify-center sm:pt-0"
    >
        <div class="absolute top-4 right-4">
            <Button
                variant="ghost"
                size="icon"
                @click="toggleTheme"
                class="rounded-lg text-muted-foreground hover:text-foreground"
            >
                <Sun v-if="isDark" class="size-5" />
                <Moon v-else class="size-5" />
            </Button>
        </div>
        
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-primary" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-card px-6 py-4 shadow-xl sm:max-w-md sm:rounded-2xl border border-border"
        >
            <slot />
        </div>
    </div>
</template>
