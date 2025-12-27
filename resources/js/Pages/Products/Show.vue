<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';
import { computed, ref, watch } from 'vue';

// Shadcn UI
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Progress } from '@/Components/ui/progress';
import { Card, CardContent } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle, 
    DialogDescription,
    DialogFooter,
    DialogTrigger
} from '@/Components/ui/dialog';
import { 
    ChevronLeft, 
    ShoppingCart, 
    Loader2, 
    Package, 
    RefreshCcw, 
    Trash2, 
    Star,
    ShieldCheck,
    Truck,
    Clock
} from 'lucide-vue-next';

const props = defineProps({
    product: Object,
});

const productData = ref({ ...props.product.data });

watch(() => props.product.data, (newData) => {
    productData.value = { ...newData };
}, { deep: true });

const { addToCart, adding } = useCart();

const handleAddToCart = async () => {
    try {
        const updatedProductView = await addToCart(productData.value);
        if (updatedProductView) {
             productData.value = { ...updatedProductView };
        }
    } catch (error) {}
};

const stockPercentage = computed(() => {
    if (!productData.value.total_stock) return 0;
    return (productData.value.stock_quantity / productData.value.total_stock) * 100;
});

// Refill Dialog State
const refillOpen = ref(false);
const deleteOpen = ref(false);

const handleRefill = () => {
    router.post(route('products.refill', productData.value.id), {}, { 
        preserveScroll: true,
        onSuccess: () => {
            refillOpen.value = false;
        }
    });
};

const handleDelete = () => {
    router.delete(route('products.destroy', productData.value.id), {
        onSuccess: () => {
            deleteOpen.value = false;
        }
    });
};
</script>

<template>
    <Head :title="productData.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                 <Link :href="route('products.index')">
                    <Button variant="ghost" size="icon" class="rounded-full hover:bg-muted transition-colors">
                        <ChevronLeft class="size-6" />
                    </Button>
                 </Link>
                 <div>
                    <h2 class="font-black text-2xl text-foreground leading-tight tracking-tight">Product Details</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs font-bold text-muted-foreground uppercase tracking-widest">Marketplace</span>
                        <span class="text-muted-foreground/30">/</span>
                        <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ productData.category }}</span>
                    </div>
                 </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                
                <!-- Left: Product Image Gallery -->
                <div class="lg:col-span-7 space-y-6">
                    <Card class="overflow-hidden border-none shadow-premium bg-card/50 backdrop-blur-xl group">
                        <div class="aspect-square relative overflow-hidden bg-muted/20 flex items-center justify-center p-12">
                            <img 
                                :src="productData.image_url" 
                                :alt="productData.name" 
                                class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal drop-shadow-2xl" 
                            />
                            
                            <!-- Status Badges -->
                            <div class="absolute top-6 left-6 flex flex-col gap-2">
                                <Badge v-if="productData.stock_status.value === 'in_stock'" class="bg-green-500 hover:bg-green-600 font-bold px-3 py-1 rounded-full shadow-lg shadow-green-500/20">
                                    Available Now
                                </Badge>
                                <Badge v-else-if="productData.stock_status.value === 'low_stock'" class="bg-orange-500 hover:bg-orange-600 font-bold px-3 py-1 rounded-full shadow-lg shadow-orange-500/20 animate-pulse">
                                    Limited Stock
                                </Badge>
                                <Badge v-else variant="destructive" class="font-bold px-3 py-1 rounded-full">
                                    Sold Out
                                </Badge>
                            </div>
                        </div>
                    </Card>

                    <!-- Trust Signals -->
                    <div class="grid grid-cols-3 gap-4">
                        <Card class="p-4 border-none bg-muted/40 text-center space-y-2">
                            <ShieldCheck class="size-5 mx-auto text-primary" />
                            <p class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Premium Quality</p>
                        </Card>
                        <Card class="p-4 border-none bg-muted/40 text-center space-y-2">
                            <Truck class="size-5 mx-auto text-primary" />
                            <p class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Fast Delivery</p>
                        </Card>
                        <Card class="p-4 border-none bg-muted/40 text-center space-y-2">
                            <Clock class="size-5 mx-auto text-primary" />
                            <p class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">24/7 Support</p>
                        </Card>
                    </div>
                </div>

                <!-- Right: Product Info & Actions -->
                <div class="lg:col-span-5 space-y-8">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center mr-2">
                                <Star v-for="i in 5" :key="i" class="size-3.5 fill-yellow-400 text-yellow-400" />
                            </div>
                            <span class="text-xs font-black text-muted-foreground tracking-widest uppercase">4.8 (124 Reviews)</span>
                        </div>
                        
                        <h1 class="text-4xl font-black text-foreground tracking-tighter leading-tight">{{ productData.name }}</h1>
                        
                        <div class="flex items-center gap-4">
                            <span class="text-3xl font-black text-primary drop-shadow-sm">{{ productData.formatted_price }}</span>
                            <Badge variant="secondary" class="font-bold text-[10px] uppercase py-0.5 px-2">Tax Included</Badge>
                        </div>

                        <p class="text-muted-foreground leading-relaxed text-lg font-medium">
                            {{ productData.description }}
                        </p>
                    </div>

                    <Separator class="bg-muted-foreground/10" />

                    <!-- Inventory Status Section -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-end">
                            <div>
                                <h3 class="text-xs font-black text-foreground uppercase tracking-widest">Inventory Status</h3>
                                <p class="text-sm font-bold text-muted-foreground mt-1">{{ productData.stock_quantity }} of {{ productData.total_stock }} units remaining</p>
                            </div>
                            <span class="text-xl font-black text-primary">{{ Math.round(stockPercentage) }}%</span>
                        </div>
                        <Progress :value="stockPercentage" class="h-3 bg-muted rounded-full overflow-hidden" />
                    </div>

                    <!-- Tags & Category -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-foreground uppercase tracking-widest">Specifications</h3>
                        <div class="flex flex-wrap gap-2">
                            <Badge variant="outline" class="rounded-lg border-muted-foreground/20 font-bold px-3 py-1 bg-card hover:bg-muted transition-colors">
                                Category: {{ productData.category }}
                            </Badge>
                            <Badge 
                                v-for="tag in productData.tags" 
                                :key="tag" 
                                variant="outline" 
                                class="rounded-lg border-primary/20 bg-primary/5 text-primary font-bold px-3 py-1"
                            >
                                #{{ tag }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 space-y-4">
                        <Button 
                            size="lg" 
                            class="w-full h-16 rounded-lg font-black text-lg shadow-2xl shadow-primary/30 transition-all duration-300 active:scale-95 group bg-primary text-primary-foreground hover:bg-primary/90"
                            @click="handleAddToCart"
                            :disabled="adding || productData.stock_quantity === 0"
                        >
                            <Loader2 v-if="adding" class="mr-3 size-6 animate-spin" />
                            <ShoppingCart v-else class="mr-3 size-6 transition-transform group-hover:-translate-y-1" />
                            {{ adding ? 'Processing Request...' : (productData.stock_quantity === 0 ? 'Out of Stock' : 'Add to Marketplace Cart') }}
                        </Button>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-muted-foreground/10">
                            <!-- Helper Dialogs Removed/Simplified if needed, but keeping for now as they are part of UI -->
                             <Button variant="outline" class="rounded-xl font-bold h-12 border-muted-foreground/20 hover:bg-muted group" disabled>
                                <RefreshCcw class="size-4 mr-2" />
                                Refill (Demo)
                            </Button>
                             <Button variant="ghost" class="rounded-xl font-bold h-12 text-destructive hover:bg-destructive/5 hover:text-destructive group" disabled>
                                <Trash2 class="size-4 mr-2" />
                                Delete (Demo)
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.shadow-premium {
    box-shadow: 0 50px 100px -20px rgba(50, 50, 93, 0.08), 0 30px 60px -30px rgba(0, 0, 0, 0.1);
}
</style>
