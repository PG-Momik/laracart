<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';
import { 
    Trash2, 
    Plus, 
    Minus, 
    ShoppingBag, 
    ArrowRight, 
    ChevronLeft, 
    Loader2, 
    ShieldCheck, 
    Truck, 
    CreditCard 
} from 'lucide-vue-next';

// Shadcn UI
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import { Badge } from '@/Components/ui/badge';
import { ScrollArea } from '@/Components/ui/scroll-area';

const { cart, updateQuantity, removeFromCart, processing } = useCart();

const handleRemove = (itemId) => {
    removeFromCart(itemId);
};

</script>

<template>
    <Head title="Shopping Cart" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                 <Link :href="route('products.index')">
                    <Button variant="ghost" size="icon" class="rounded-full hover:bg-muted">
                        <ChevronLeft class="size-6" />
                    </Button>
                 </Link>
                 <div>
                    <h2 class="font-black text-2xl text-foreground leading-tight tracking-tight">Shopping Cart</h2>
                    <p class="text-xs font-bold text-muted-foreground uppercase tracking-widest mt-0.5">
                        {{ cart.items.length }} items in your bag
                    </p>
                 </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div v-if="cart.items.length > 0">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Cart Items Section -->
                    <div class="lg:col-span-8 space-y-6">
                        <Card class="border-none shadow-soft bg-card/50 backdrop-blur-sm overflow-hidden">
                            <CardHeader class="pb-2">
                                <CardTitle class="text-lg font-black uppercase tracking-widest text-muted-foreground">Order Items</CardTitle>
                            </CardHeader>
                            <CardContent class="p-0">
                                <ul role="list" class="divide-y divide-muted-foreground/5">
                                    <li v-for="item in cart.items" :key="item.id" class="p-6 transition-all duration-300 hover:bg-muted/30 group">
                                        <div class="flex gap-6">
                                            <!-- Product Image -->
                                            <div class="relative size-24 sm:size-32 bg-muted/20 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center p-4">
                                                <img 
                                                    :src="item.product.image_url" 
                                                    :alt="item.product.name" 
                                                    class="size-full object-contain mix-blend-multiply dark:mix-blend-normal transition-transform duration-500 group-hover:scale-110" 
                                                />
                                                <button 
                                                    @click="handleRemove(item.id)"
                                                    class="absolute top-2 left-2 size-10 bg-white shadow-premium text-destructive rounded-full opacity-0 scale-75 blur-sm group-hover:opacity-100 group-hover:scale-100 group-hover:blur-0 transition-all duration-500 hover:bg-destructive hover:text-white flex items-center justify-center z-10 border border-border"
                                                >
                                                    <Trash2 class="size-4" />
                                                </button>
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1 flex flex-col justify-between py-1">
                                                <div class="space-y-1">
                                                    <div class="flex justify-between items-start">
                                                        <h3 class="text-lg font-black text-foreground group/link">
                                                            <Link :href="route('products.show', item.product.id)" class="group-hover/link:text-primary transition-colors">
                                                                {{ item.product.name }}
                                                            </Link>
                                                        </h3>
                                                        <p class="text-lg font-black text-primary">{{ item.formatted_subtotal }}</p>
                                                    </div>
                                                    <p class="text-xs font-bold text-muted-foreground uppercase tracking-wider">{{ item.product.category }}</p>
                                                    <p class="text-sm text-muted-foreground line-clamp-1 italic max-w-sm mt-1">{{ item.product.description }}</p>
                                                </div>

                                                <div class="flex items-center justify-between mt-4">
                                                    <!-- Quantity Selector -->
                                                    <div class="flex items-center bg-muted/50 rounded-lg p-1 border border-muted-foreground/10">
                                                        <Button 
                                                            variant="ghost" 
                                                            size="icon" 
                                                            class="size-8 rounded-lg hover:bg-background shadow-sm transition-all"
                                                            :disabled="item.quantity <= 1 || processing"
                                                            @click="updateQuantity(item.id, item.quantity - 1)"
                                                        >
                                                            <Minus class="size-3.5" />
                                                        </Button>
                                                        <span class="w-10 text-center text-sm font-black">{{ item.quantity }}</span>
                                                        <Button 
                                                            variant="ghost" 
                                                            size="icon" 
                                                            class="size-8 rounded-lg hover:bg-background shadow-sm transition-all"
                                                            :disabled="item.product.stock_quantity === 0 || processing"
                                                            @click="updateQuantity(item.id, item.quantity + 1)"
                                                        >
                                                            <Plus class="size-3.5" />
                                                        </Button>
                                                    </div>

                                                    <!-- Stock Indicator -->
                                                    <div class="flex items-center gap-2">
                                                        <div :class="['size-1.5 rounded-full ring-4 ring-opacity-20', item.product.stock_quantity > 0 ? 'bg-green-500 ring-green-500' : 'bg-destructive ring-destructive']"></div>
                                                        <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">
                                                            {{ item.product.stock_quantity > 0 ? 'In Stock' : 'Last items' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </CardContent>
                        </Card>

                        <!-- Trust Signals -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="flex items-center gap-3 p-4 bg-primary/5 rounded-xl border border-primary/10">
                                <Truck class="size-5 text-primary" />
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Free Shipping</p>
                                    <p class="text-[10px] text-muted-foreground font-bold">On orders over $500</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-primary/5 rounded-xl border border-primary/10">
                                <ShieldCheck class="size-5 text-primary" />
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Secure Payment</p>
                                    <p class="text-[10px] text-muted-foreground font-bold">SSL Encrypted 256-bit</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-primary/5 rounded-xl border border-primary/10">
                                <CreditCard class="size-5 text-primary" />
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Money Back</p>
                                    <p class="text-[10px] text-muted-foreground font-bold">30-day guarantee</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="lg:col-span-4 sticky top-24">
                        <Card class="border-none shadow-premium bg-card/50 backdrop-blur-xl overflow-hidden">
                            <CardHeader>
                                <CardTitle class="text-xl font-black">Order Summary</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-6">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="font-bold text-muted-foreground uppercase tracking-widest">Subtotal</span>
                                        <span class="font-black text-foreground">${{ (cart.total).toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="font-bold text-muted-foreground uppercase tracking-widest">Shipping Estimate</span>
                                        <span class="font-bold text-green-600 uppercase tracking-widest">Free</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="font-bold text-muted-foreground uppercase tracking-widest">Tax Estimate</span>
                                        <span class="font-black text-foreground">$0.00</span>
                                    </div>
                                </div>
                                <Separator class="bg-muted-foreground/10" />
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-black uppercase tracking-widest">Total Amount</span>
                                    <span class="text-3xl font-black text-primary transition-all duration-500">${{ (cart.total).toFixed(2) }}</span>
                                </div>
                            </CardContent>
                            <CardFooter class="flex flex-col gap-3 p-6 pt-0">
                                <Link 
                                    :href="route('checkout.index')"
                                    class="w-full"
                                    :disabled="processing || cart.items.some(i => !i.has_sufficient_stock)"
                                >
                                    <Button 
                                        size="lg" 
                                        class="w-full h-14 rounded-xl font-black text-lg shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95 group"
                                    >
                                        <Loader2 v-if="processing" class="mr-2 size-5 animate-spin" />
                                        <span v-else class="flex items-center">
                                            Proceed to Checkout
                                            <ArrowRight class="ml-2 size-5 transition-transform group-hover:translate-x-1" />
                                        </span>
                                    </Button>
                                </Link>
                                <Link :href="route('products.index')" class="w-full">
                                    <Button variant="ghost" class="w-full rounded-lg font-bold text-muted-foreground hover:text-primary transition-colors uppercase tracking-widest text-[10px]">
                                        Continue Shopping
                                    </Button>
                                </Link>
                            </CardFooter>
                        </Card>

                        <!-- Discount Info -->
                        <div class="mt-6 text-center">
                            <p class="text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em]">Have a promo code? Add it at checkout.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart State -->
            <div v-else class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4 animate-in fade-in slide-in-from-bottom-4 duration-1000">
                <div class="size-32 bg-primary/5 rounded-full flex items-center justify-center mb-8 relative">
                    <ShoppingBag class="size-16 text-primary/40 stroke-[1.5]" />
                    <div class="absolute inset-0 bg-primary/10 rounded-full animate-ping delay-300"></div>
                </div>
                <h2 class="text-4xl font-black text-foreground tracking-tighter mb-4">Your bag is empty</h2>
                <p class="text-muted-foreground max-w-sm mb-12 font-medium">It feels a bit lonely here. Let's find some products to fill it up!</p>
                
                <Link :href="route('products.index')">
                    <Button size="lg" class="h-14 px-12 rounded-xl font-black text-lg shadow-2xl shadow-primary/20 group">
                        Explore Marketplace
                        <ArrowRight class="ml-2 size-5 transition-transform group-hover:translate-x-1" />
                    </Button>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.shadow-soft {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 5px 15px -10px rgba(0, 0, 0, 0.05);
}

.shadow-premium {
    box-shadow: 0 50px 100px -20px rgba(50, 50, 93, 0.1), 0 30px 60px -30px rgba(0, 0, 0, 0.15);
}
</style>
