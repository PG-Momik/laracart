<script setup>
import {useCart} from '@/Composables/useCart';
import {Button} from '@/Components/ui/button';
import {Loader2, ShoppingCart} from 'lucide-vue-next';

const props = defineProps({
  product: Object,
});

const {addToCart, adding} = useCart();

const handleAddToCart = async () => {
  try {
    const updatedProduct = await addToCart(props.product);
    if (updatedProduct) {
      Object.assign(props.product, updatedProduct);
    }
  } catch (error) {
  }
};
</script>

<template>
  <Button
      :disabled="adding || product.stock_quantity === 0"
      :variant="product.stock_quantity === 0 ? 'secondary' : 'default'"
      class="rounded-lg font-bold transition-all duration-300 active:scale-95 group/btn h-10 px-4 flex items-center gap-2"
      @click="handleAddToCart"
  >
    <Loader2 v-if="adding" class="size-4 animate-spin"/>
    <ShoppingCart v-else
                  class="size-4 transition-transform group-hover/btn:-translate-y-0.5 group-hover/btn:translate-x-0.5"/>
    {{ adding ? 'Adding...' : (product.stock_quantity === 0 ? 'Out of Stock' : 'Add to Cart') }}
  </Button>
</template>
