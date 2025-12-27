<script setup>
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Progress } from '@/Components/ui/progress';
import { ShoppingCart, Loader2, Star } from 'lucide-vue-next';

const props = defineProps({
    product: Object,
});

const { addToCart, adding } = useCart();

const handleAddToCart = async () => {
    try {
        const updatedProduct = await addToCart(props.product);
        if (updatedProduct) {
            Object.assign(props.product, updatedProduct);
        }
    } catch (error) {}
};

const getStatusVariant = (status) => {
    switch (status) {
        case 'in_stock': return 'default';
        case 'low_stock': return 'warning';
        case 'out_of_stock': return 'destructive';
        default: return 'secondary';
    }
};
</script>

<template>
    <Card class="group overflow-hidden border-none shadow-soft hover:shadow-hover transition-all duration-500 bg-card/40 backdrop-blur-sm">
        <div class="relative aspect-square overflow-hidden bg-muted/30">
            <!-- Product Image -->
            <img 
                :src="product.image_url" 
                :alt="product.name"
                loading="eager"
                decoding="async"
                class="h-full w-full object-contain p-6" 
            />

            


            <div class="absolute top-3 right-3">
                <Badge 
                    v-if="product.stock_quantity === 0"
                    variant="destructive" 
                    class="rounded-full px-3 py-1 font-bold shadow-lg animate-pulse"
                >
                    Sold Out
                </Badge>
                <Badge 
                    v-else-if="product.stock_status.value === 'low_stock'" 
                    class="bg-orange-500 hover:bg-orange-600 text-white border-none rounded-full px-3 py-1 font-bold shadow-lg"
                >
                    Low Stock
                </Badge>
            </div>

            <!-- Overlays/Actions could go here -->
            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" />
        </div>

        <CardContent class="p-5 flex flex-col gap-2.5">
            <div class="space-y-1">
                <div class="flex justify-between items-start gap-2">
                    <h3 class="font-bold text-lg text-foreground leading-tight group-hover:text-primary transition-colors line-clamp-1">
                        <Link :href="route('products.show', product.id)">{{ product.name }}</Link>
                    </h3>
                    <div class="text-lg font-black text-primary drop-shadow-sm whitespace-nowrap">
                        {{ product.formatted_price }}
                    </div>
                </div>
                
                <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed min-h-[2rem]">
                    {{ product.description }}
                </p>
            </div>

            <!-- Tags -->
            <div v-if="product.tags?.length" class="flex flex-wrap gap-1.5 mt-1">
                <span v-for="tag in product.tags.slice(0, 3)" :key="tag" class="text-[10px] font-semibold text-muted-foreground bg-muted px-2 py-0.5 rounded-sm">
                    #{{ tag }}
                </span>
            </div>

            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest text-muted-foreground/60 mt-2">
                <span class="flex items-center gap-1">
                    <Star class="size-3 fill-yellow-400 text-yellow-400" />
                    <span class="text-foreground">4.8</span>
                </span>
                <span>{{ product.stock_quantity }} units left</span>
            </div>
        </CardContent>

        <CardFooter class="p-5 pt-0">
            <Button 
                @click="handleAddToCart"
                :disabled="adding || product.stock_quantity === 0"
                :variant="product.stock_quantity === 0 ? 'secondary' : 'default'"
                class="w-full rounded-lg font-bold transition-all duration-300 hover:shadow-lg hover:shadow-primary/20 active:scale-95 group/btn"
            >
                <Loader2 v-if="adding" class="mr-2 size-4 animate-spin" />
                <ShoppingCart v-else class="mr-2 size-4 transition-transform group-hover/btn:-translate-y-0.5 group-hover/btn:translate-x-0.5" />
                {{ adding ? 'Processing...' : (product.stock_quantity === 0 ? 'Out of Stock' : 'Add to Cart') }}
            </Button>
        </CardFooter>
    </Card>
</template>

<style scoped>
.shadow-soft {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
}
.shadow-hover {
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.05);
}
</style>
