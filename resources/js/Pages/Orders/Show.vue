<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ChevronLeft, 
    Calendar, 
    Package, 
    CheckCircle2, 
    Truck, 
    HelpCircle, 
    ShieldCheck, 
    Mail,
    ArrowRight,
    ShoppingBag
} from 'lucide-vue-next';

// Shadcn UI
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Separator } from '@/Components/ui/separator';

const props = defineProps({
    order: Object,
});

const orderData = props.order.data;

const getStatusClasses = (color) => {
    const map = {
        gray: 'bg-muted text-muted-foreground border-muted-foreground/20',
        blue: 'bg-primary/10 text-primary border-primary/20',
        green: 'bg-green-500/10 text-green-600 border-green-500/20',
        red: 'bg-destructive/10 text-destructive border-destructive/20',
    };
    return map[color] || map.gray;
};
</script>

<template>
    <Head :title="`Order #${orderData.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('orders.index')">
                    <Button variant="ghost" size="icon" class="rounded-full hover:bg-muted transition-colors">
                        <ChevronLeft class="size-5" />
                    </Button>
                </Link>
                <div>
                    <h2 class="text-3xl font-black text-foreground leading-tight tracking-tight">Order Details</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs font-bold text-muted-foreground uppercase tracking-widest">Reference No.</span>
                        <span class="text-xs font-black text-primary uppercase tracking-widest">#{{ orderData.id }}</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Main Content Card -->
            <Card class="border-none shadow-premium bg-card/60 backdrop-blur-xl overflow-hidden rounded-2xl">
                <CardContent class="p-8 sm:p-12">
                    <!-- Meta Info Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 pb-10 border-b border-muted-foreground/10">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-muted-foreground">
                                <Calendar class="size-4" />
                                <p class="text-[10px] font-black uppercase tracking-[0.2em]">Acquisition Date</p>
                            </div>
                            <p class="text-xl font-black text-foreground">{{ orderData.formatted_date }}</p>
                        </div>
                        <div class="flex flex-col sm:items-end gap-3 w-full sm:w-auto">
                            <div class="flex items-center gap-2 text-muted-foreground sm:justify-end">
                                <Package class="size-4" />
                                <p class="text-[10px] font-black uppercase tracking-[0.2em]">Lifecycle Status</p>
                            </div>
                            <Badge 
                                :class="['px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border-2 shadow-sm', getStatusClasses(orderData.status.color)]"
                            >
                                <span :class="['size-1.5 rounded-full mr-2 animate-pulse', orderData.status.color === 'green' ? 'bg-green-500' : (orderData.status.color === 'blue' ? 'bg-primary' : 'bg-muted-foreground')]"></span>
                                {{ orderData.status.label }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Items Table / List -->
                    <div class="pt-12 space-y-8">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-muted-foreground uppercase tracking-[0.3em]">Curated Manifest</h4>
                            <div class="flex items-center gap-2 px-3 py-1 bg-muted/30 rounded-full">
                                <span class="size-2 bg-primary rounded-full animate-ping"></span>
                                <span class="text-[8px] font-black uppercase text-muted-foreground tracking-widest">Verified Digital Supply</span>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div v-for="item in orderData.items" :key="item.id" class="flex gap-8 items-center bg-muted/10 p-4 rounded-xl border border-muted-foreground/5 hover:bg-muted/20 transition-all group">
                                <div class="relative flex-shrink-0">
                                    <div class="size-24 sm:size-28 rounded-xl border border-muted-foreground/10 bg-white flex items-center justify-center p-4 shadow-soft overflow-hidden group-hover:scale-105 transition-transform duration-500">
                                        <img 
                                            v-if="item.product?.image_url" 
                                            :src="item.product.image_url" 
                                            :alt="item.product.name" 
                                            class="size-full object-contain" 
                                        />
                                        <div v-else class="flex flex-col items-center justify-center text-muted-foreground/20">
                                            <Package class="size-8" />
                                            <span class="text-[8px] font-black mt-1">NO IMAGE</span>
                                        </div>
                                    </div>
                                    <Badge class="absolute -top-3 -right-3 size-8 rounded-lg bg-primary shadow-lg border-4 border-card font-black text-xs flex items-center justify-center">
                                        {{ item.quantity }}
                                    </Badge>
                                </div>
                                <div class="flex-grow space-y-2">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                        <h4 class="text-xl font-black text-foreground tracking-tight group-hover:text-primary transition-colors">{{ item.product.name }}</h4>
                                        <p class="text-xl font-black text-foreground">{{ item.formatted_subtotal }}</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <Badge variant="secondary" class="text-[9px] font-black uppercase tracking-widest px-2">{{ item.product.category }}</Badge>
                                        <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
                                            {{ item.quantity }} Units &times; {{ item.formatted_price }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="mt-16 pt-12 border-t border-muted-foreground/10 flex justify-end">
                        <Card class="w-full sm:max-w-md border-none bg-muted/30 p-8 rounded-xl">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-xs font-black text-muted-foreground uppercase tracking-widest">
                                    <span>Value</span>
                                    <span class="text-foreground font-black">{{ orderData.formatted_total }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs font-black text-muted-foreground uppercase tracking-widest">
                                    <span>Logistic Costs</span>
                                    <span class="text-green-600 font-black">COMPLIMENTARY</span>
                                </div>
                                <div class="flex justify-between items-center text-xs font-black text-muted-foreground uppercase tracking-widest">
                                    <span>Calculated Tax</span>
                                    <span class="text-foreground font-black">INCLUDED</span>
                                </div>
                                <Separator class="bg-muted-foreground/10 my-4" />
                                <div class="flex justify-between items-end pt-2">
                                    <div class="space-y-1">
                                        <h5 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Total</h5>
                                        <p class="text-4xl font-black text-primary tracking-tighter">{{ orderData.formatted_total }}</p>
                                    </div>
                                    <div class="pb-1">
                                        <p class="text-[10px] font-black text-muted-foreground">USD</p>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                </CardContent>

                <div class="bg-primary p-8 sm:p-12 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-8 relative overflow-hidden">
                    <div class="relative z-10 space-y-2">
                        <div class="flex items-center gap-3 justify-center sm:justify-start">
                            <CheckCircle2 class="size-6 text-primary-foreground" />
                            <h3 class="text-2xl font-black text-primary-foreground tracking-tight">Order Successfully Authorized</h3>
                        </div>
                        <p class="text-primary-foreground/70 font-medium text-sm">Our logistics experts are preparing your shipment for express delivery.</p>
                    </div>
                    <Link :href="route('products.index')" class="relative z-10 w-full sm:w-auto">
                        <Button size="lg" variant="primary" class="w-full sm:px-10 h-14 rounded-md font-black text-sm uppercase tracking-widest shadow-xl shadow-black/10 bg-secondary">
                            Re-enter Marketplace
                            <ArrowRight class="ml-2 size-4" />
                        </Button>
                    </Link>
                </div>
            </Card>

            <!-- Support & Policy -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <div class="bg-card/40 p-6 rounded-xl border border-muted-foreground/10 flex items-start gap-4 shadow-soft hover:shadow-premium transition-all duration-500">
                    <div class="size-12 bg-primary/5 rounded-md flex items-center justify-center flex-shrink-0 group">
                        <HelpCircle class="size-5 text-primary group-hover:scale-110 transition-transform" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-foreground uppercase tracking-widest mb-1.5 md:mb-1">Priority Support</h4>
                        <p class="text-[10px] text-muted-foreground font-black leading-relaxed uppercase tracking-tight">Access 24/7 dedicated assistance for your purchase queries.</p>
                    </div>
                 </div>
                 <div class="bg-card/40 p-6 rounded-xl border border-muted-foreground/10 flex items-start gap-4 shadow-soft hover:shadow-premium transition-all duration-500">
                    <div class="size-12 bg-primary/5 rounded-md flex items-center justify-center flex-shrink-0 group">
                        <ShieldCheck class="size-5 text-primary group-hover:scale-110 transition-transform" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-foreground uppercase tracking-widest mb-1.5 md:mb-1">Secure Integrity</h4>
                        <p class="text-[10px] text-muted-foreground font-black leading-relaxed uppercase tracking-tight">End-to-end encryption ensures your financial metadata stays private.</p>
                    </div>
                 </div>
                 <div class="bg-card/40 p-6 rounded-xl border border-muted-foreground/10 flex items-start gap-4 shadow-soft hover:shadow-premium transition-all duration-500">
                    <div class="size-12 bg-primary/5 rounded-md flex items-center justify-center flex-shrink-0 group">
                        <Mail class="size-5 text-primary group-hover:scale-110 transition-transform" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-foreground uppercase tracking-widest mb-1.5 md:mb-1">Invoice Receipt</h4>
                        <p class="text-[10px] text-muted-foreground font-black leading-relaxed uppercase tracking-tight">A verifiable digital copy has been dispatched to your mailbox.</p>
                    </div>
                 </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.shadow-soft {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
}

.shadow-premium {
    box-shadow: 0 50px 100px -20px rgba(50, 50, 93, 0.08), 0 30px 60px -30px rgba(0, 0, 0, 0.1);
}
</style>
