<script setup>
import {useVModel} from "@vueuse/core";
import {cn} from "@/lib/utils";

const props = defineProps({
  defaultValue: {type: [String, Number], required: false},
  modelValue: {type: [String, Number], required: false},
  class: {type: null, required: false},
});

const emits = defineEmits(["update:modelValue"]);

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
});
</script>

<template>
  <input
      v-model="modelValue"
      :class="
      cn(
        'flex h-10 w-full rounded-md border border-input bg-background/50 backdrop-blur-sm px-4 py-2 text-sm shadow-subtle transition-all duration-200 file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground/60 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-1 focus-visible:border-ring/50 hover:border-input/80 disabled:cursor-not-allowed disabled:opacity-50',
        props.class,
      )
    "
  />
</template>
