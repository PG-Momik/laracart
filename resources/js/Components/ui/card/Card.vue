<script setup>
import {cn} from "@/lib/utils";

const props = defineProps({
  class: {type: null, required: false},
});
</script>

<template>
  <div
      :class="
      cn('rounded-lg border border-border/50 bg-card text-card-foreground shadow-subtle backdrop-blur-sm', props.class)
    "
  >
    <slot/>
  </div>
</template>
