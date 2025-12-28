<script setup>
import {reactiveOmit} from "@vueuse/core";
import {Label} from "reka-ui";
import {cn} from "@/lib/utils";

const props = defineProps({
  for: {type: String, required: false},
  asChild: {type: Boolean, required: false},
  as: {type: null, required: false},
  class: {type: null, required: false},
});

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
  <Label
      :class="
      cn(
        'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
        props.class,
      )
    "
      v-bind="delegatedProps"
  >
    <slot/>
  </Label>
</template>
