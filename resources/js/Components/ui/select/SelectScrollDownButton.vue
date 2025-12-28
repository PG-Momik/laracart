<script setup>
import {reactiveOmit} from "@vueuse/core";
import {ChevronDown} from "lucide-vue-next";
import {SelectScrollDownButton, useForwardProps} from "reka-ui";
import {cn} from "@/lib/utils";

const props = defineProps({
  asChild: {type: Boolean, required: false},
  as: {type: null, required: false},
  class: {type: null, required: false},
});

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
  <SelectScrollDownButton
      :class="
      cn('flex cursor-default items-center justify-center py-1', props.class)
    "
      v-bind="forwardedProps"
  >
    <slot>
      <ChevronDown/>
    </slot>
  </SelectScrollDownButton>
</template>
