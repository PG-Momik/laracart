<script lang="ts" setup>
import {onMounted} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link} from '@inertiajs/vue3';
import {Moon, Sun} from 'lucide-vue-next';
import {Button} from '@/Components/ui/button';
import {useTheme} from '@/Composables/useTheme';

const {isDark, initTheme, toggleTheme} = useTheme();

onMounted(() => {
  initTheme();
});
</script>

<template>
  <div
      class="flex min-h-screen flex-col items-center bg-background pt-6 sm:justify-center sm:pt-0 relative overflow-hidden">
    <!-- Background Decorations -->
    <div
        class="absolute -top-[10%] -left-[10%] size-[400px] bg-primary/5 rounded-full blur-[120px] pointer-events-none"/>
    <div
        class="absolute -bottom-[10%] -right-[10%] size-[400px] bg-primary/10 rounded-full blur-[120px] pointer-events-none"/>

    <div class="absolute top-6 right-6 z-50">
      <Button
          class="rounded-md bg-background/50 backdrop-blur-sm border border-border/50 text-muted-foreground hover:text-foreground transition-all h-10 w-10"
          size="icon"
          variant="ghost"
          @click="toggleTheme"
      >
        <Sun v-if="isDark" class="size-5"/>
        <Moon v-else class="size-5"/>
      </Button>
    </div>

    <div class="relative z-10 mb-8 transition-transform hover:scale-110 duration-500">
      <Link href="/">
        <div
            class="size-20 bg-primary/10 rounded-2xl flex items-center justify-center backdrop-blur-xl border border-primary/20">
          <ApplicationLogo class="h-10 w-10 text-primary"/>
        </div>
      </Link>
    </div>

    <div
        class="relative z-10 mt-6 w-full overflow-hidden bg-card/60 backdrop-blur-2xl px-8 py-10 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] sm:max-w-md sm:rounded-xl border border-border/50"
    >
      <slot/>
    </div>

    <!-- Footer Info -->
    <div class="relative z-10 mt-12 text-center">
      <p class="text-[10px] font-black uppercase tracking-[0.4em] text-muted-foreground/40">Lara Cart &bull; Secure
        Authentication</p>
    </div>
  </div>
</template>

