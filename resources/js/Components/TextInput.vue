<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="flex h-10 w-full rounded-md border border-input bg-background/50 backdrop-blur-sm px-4 py-2 text-sm shadow-subtle transition-all duration-200 placeholder:text-muted-foreground/60 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-1 focus-visible:border-ring/50 hover:border-input/80 disabled:cursor-not-allowed disabled:opacity-50"
        v-model="model"
        ref="input"
    />
</template>
