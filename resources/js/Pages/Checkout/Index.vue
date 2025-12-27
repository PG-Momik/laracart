<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    ChevronLeft, 
    CreditCard, 
    ShieldCheck, 
    Lock, 
    Info, 
    Loader2, 
    CheckCircle2, 
    ArrowRight,
    Wallet
} from 'lucide-vue-next';

// Shadcn UI
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Separator } from '@/Components/ui/separator';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    cart: Object,
});

const processing = ref(false);
const paymentMethod = ref('stripe'); // 'stripe' or 'paypal'
const email = ref(props.cart.user.email);
const cardName = ref(props.cart.user.name);
const step = ref('form'); // 'form', 'validating', 'success'

const handleSubmit = () => {
    processing.value = true;
    step.value = 'validating';
    
    setTimeout(() => {
        router.post(route('checkout.store'), {}, {
            onFinish: () => {
                processing.value = false;
            },
        });
    }, 2500);
};

const handlePaypalClick = () => {
    processing.value = true;
    step.value = 'validating';
    
    setTimeout(() => {
        router.post(route('checkout.store'));
    }, 3000);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};
</script>

<template>
    <Head title="Secure Checkout" />

    <!-- Premium Notice Banner -->
    <div class="bg-primary/10 border-b border-primary/20 text-primary px-4 py-2 text-center text-[10px] font-black uppercase tracking-[0.2em] backdrop-blur-md sticky top-0 z-50">
        <span class="bg-primary text-white px-2 py-0.5 rounded-sm mr-2 text-[8px]">Notice</span> 
        Demo Environment: Payment gateways are simulated for architectural demonstration.
    </div>

    <div class="min-h-screen bg-background flex flex-col lg:grid lg:grid-cols-12 relative overflow-hidden">
        
        <!-- Processing Overlay -->
        <transition
            enter-active-class="transition ease-out duration-500"
            enter-from-class="opacity-0 backdrop-blur-0"
            enter-to-class="opacity-100 backdrop-blur-xl"
            leave-active-class="transition ease-in duration-300"
            leave-from-class="opacity-100 backdrop-blur-xl"
            leave-to-class="opacity-0 backdrop-blur-0"
        >
            <div v-if="processing" class="fixed inset-0 z-[100] bg-background/80 backdrop-blur-xl flex flex-col items-center justify-center p-8 text-center">
                <div class="mb-12 relative">
                    <div class="size-24 rounded-3xl bg-primary/5 flex items-center justify-center relative overflow-hidden">
                        <Loader2 class="size-12 text-primary animate-spin-slow stroke-[1.5]" />
                        <div class="absolute inset-0 bg-primary/10 animate-pulse"></div>
                    </div>
                </div>
                <h2 class="text-3xl font-black text-foreground tracking-tighter mb-4">
                    {{ paymentMethod === 'stripe' ? 'Authorizing Card...' : 'Connecting PayPal...' }}
                </h2>
                <div class="max-w-xs space-y-4">
                    <p class="text-muted-foreground font-medium leading-relaxed">
                        Please keep this window open while we securely verify your payment details with our global network.
                    </p>
                    <div class="flex items-center justify-center gap-2 px-4 py-2 bg-muted/50 rounded-full border border-muted-foreground/10">
                        <Lock class="size-3.5 text-primary" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Encrypted Channel</span>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Left Section: Premium Order Summary -->
        <div class="lg:col-span-5 bg-muted/30 p-8 lg:p-16 lg:min-h-screen border-r border-muted-foreground/5 relative overflow-y-auto">
            <div class="max-w-lg mx-auto lg:mr-0 lg:ml-auto w-full space-y-12">
                <Link :href="route('cart.index')">
                    <Button variant="ghost" class="text-muted-foreground hover:text-primary transition-colors gap-2 -ml-4 px-4 font-bold uppercase tracking-widest text-[10px]">
                        <ChevronLeft class="size-4" />
                        Return to Bag
                    </Button>
                </Link>

                <div class="space-y-2">
                    <h2 class="text-muted-foreground text-xs font-black uppercase tracking-[0.3em]">Checkout Summary</h2>
                    <div class="flex items-baseline gap-3">
                        <span class="text-5xl font-black text-foreground tracking-tighter">{{ formatPrice(cart.total) }}</span>
                        <span class="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-1">USD</span>
                    </div>
                </div>

                <!-- Product List -->
                <div class="space-y-6">
                    <div v-for="item in cart.items" :key="item.id" class="flex items-center gap-4 group">
                        <div class="relative flex-shrink-0">
                            <div class="size-20 bg-card rounded-2xl border border-muted-foreground/10 flex items-center justify-center p-3 shadow-soft group-hover:scale-105 transition-transform">
                                <img :src="item.product?.image_url" :alt="item.product?.name" class="size-full object-contain mix-blend-multiply dark:mix-blend-normal" />
                            </div>
                            <span class="absolute -top-2 -right-2 size-6 bg-primary text-white text-[10px] font-black rounded-lg flex items-center justify-center border-2 border-muted/50 shadow-lg">
                                {{ item.quantity }}
                            </span>
                        </div>
                        <div class="flex-grow min-w-0">
                            <h3 class="text-sm font-black text-foreground truncate">{{ item.product?.name }}</h3>
                            <p class="text-xs font-bold text-muted-foreground uppercase tracking-widest mt-0.5">{{ item.product?.category }}</p>
                        </div>
                        <div class="text-sm font-black text-foreground">
                            {{ formatPrice(item.product.price * item.quantity) }}
                        </div>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="space-y-4 pt-8 border-t border-muted-foreground/10">
                    <div class="flex justify-between items-center text-sm font-bold text-muted-foreground">
                        <span class="uppercase tracking-widest">Subtotal</span>
                        <span>{{ formatPrice(cart.total) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-muted-foreground uppercase tracking-widest">Delivery Fee</span>
                        <Badge class="bg-green-500/10 text-green-600 hover:bg-green-500/20 border-green-500/20 font-black text-[10px]">COMPLIMENTARY</Badge>
                    </div>
                    <Separator class="bg-muted-foreground/10" />
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-lg font-black uppercase tracking-widest">Amount Due</span>
                        <span class="text-2xl font-black text-primary">{{ formatPrice(cart.total) }}</span>
                    </div>
                </div>

                <!-- Trust signals -->
                <div class="grid grid-cols-2 gap-4">
                    <Card class="p-4 border-none bg-background/50 shadow-soft space-y-2">
                        <Lock class="size-5 text-primary" />
                        <p class="text-[10px] font-black uppercase tracking-widest leading-relaxed">Secure<br/>Environment</p>
                    </Card>
                    <Card class="p-4 border-none bg-background/50 shadow-soft space-y-2">
                        <CheckCircle2 class="size-5 text-primary" />
                        <p class="text-[10px] font-black uppercase tracking-widest leading-relaxed">Verified<br/>Purchase</p>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Right Section: Secure Payment Form -->
        <div class="lg:col-span-7 p-8 lg:p-20 flex flex-col bg-background relative overflow-y-auto">
            <div class="w-full max-w-md mx-auto space-y-12">
                <div class="space-y-2">
                    <h1 class="text-4xl font-black text-foreground tracking-tighter">Payment Details</h1>
                    <p class="text-muted-foreground font-medium">Select your preferred secure payment method.</p>
                </div>

                <!-- Premium Method Selector -->
                <div class="flex gap-4 p-2 bg-muted/40 rounded-3xl border border-muted-foreground/10">
                    <button 
                        @click="paymentMethod = 'stripe'"
                        :class="[
                            'flex-1 flex gap-3 items-center justify-center py-4 rounded-2xl text-sm font-black transition-all relative overflow-hidden group',
                            paymentMethod === 'stripe' ? 'bg-background shadow-premium text-foreground border border-muted-foreground/10' : 'text-muted-foreground hover:text-foreground'
                        ]"
                    >
                        <CreditCard :class="['size-5 transition-colors', paymentMethod === 'stripe' ? 'text-primary' : 'text-muted-foreground']" />
                        <span>Card Payment</span>
                        <div v-if="paymentMethod === 'stripe'" class="absolute bottom-0 left-1/2 -translate-x-1/2 w-8 h-1 bg-primary rounded-full"></div>
                    </button>
                    <button 
                        @click="paymentMethod = 'paypal'"
                        :class="[
                            'flex-1 flex gap-3 items-center justify-center py-4 rounded-2xl text-sm font-black transition-all relative overflow-hidden group',
                            paymentMethod === 'paypal' ? 'bg-background shadow-premium text-foreground border border-muted-foreground/10' : 'text-muted-foreground hover:text-foreground'
                        ]"
                    >
                        <Wallet :class="['size-5 transition-colors', paymentMethod === 'paypal' ? 'text-[#0070e0]' : 'text-muted-foreground']" />
                        <span>PayPal Bag</span>
                        <div v-if="paymentMethod === 'paypal'" class="absolute bottom-0 left-1/2 -translate-x-1/2 w-8 h-1 bg-primary rounded-full"></div>
                    </button>
                </div>

                <!-- Animated Forms -->
                <transition
                    enter-active-class="transition ease-out duration-500"
                    enter-from-class="opacity-0 translate-y-8"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-300"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-8"
                    mode="out-in"
                >
                    <div v-if="paymentMethod === 'stripe'" key="stripe" class="space-y-8">
                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <div class="space-y-2.5">
                                <Label class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1">Contact Metadata</Label>
                                <Input v-model="email" type="email" placeholder="email@example.com" class="h-14 rounded-2xl border-muted-foreground/10 bg-muted/20 focus-visible:ring-primary px-5 font-bold" />
                            </div>

                            <div class="space-y-2.5">
                                <div class="flex justify-between items-center ml-1">
                                    <Label class="text-xs font-black uppercase tracking-widest text-muted-foreground">Encryption Matrix Details</Label>
                                    <div class="flex gap-1">
                                        <div class="w-6 h-4 bg-primary/20 rounded shadow-sm"></div>
                                        <div class="w-6 h-4 bg-primary/10 rounded shadow-sm"></div>
                                    </div>
                                </div>
                                <div class="space-y-0 divide-y divide-muted-foreground/10 border border-muted-foreground/10 rounded-2xl overflow-hidden shadow-soft bg-muted/5">
                                    <div class="h-14 flex items-center px-5 gap-3 bg-background/50">
                                        <CreditCard class="size-5 text-muted-foreground" />
                                        <input type="text" disabled value="4242 4242 4242 4242" class="flex-grow bg-transparent border-none p-0 text-base font-bold tracking-widest pointer-events-none" />
                                    </div>
                                    <div class="flex h-14 bg-background/30">
                                        <input type="text" disabled value="12 / 28" class="w-1/2 px-5 bg-transparent border-r border-muted-foreground/10 text-base font-bold pointer-events-none text-center" />
                                        <input type="text" disabled value="123" class="w-1/2 px-5 bg-transparent text-base font-bold pointer-events-none text-center" />
                                    </div>
                                </div>
                                <p class="text-[10px] text-muted-foreground font-medium italic ml-1">* Using simulated secure card 4242</p>
                            </div>

                            <div class="space-y-2.5">
                                <Label class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1">Authorized Holder</Label>
                                <Input v-model="cardName" type="text" placeholder="CARDHOLDER NAME" class="h-14 rounded-2xl border-muted-foreground/10 bg-muted/20 focus-visible:ring-primary px-5 font-bold uppercase" />
                            </div>

                            <Button type="submit" class="w-full h-16 rounded-2xl font-black text-lg shadow-2xl shadow-primary/30 transition-all hover:scale-[1.02] active:scale-95 group">
                                Authorize & Pay {{ formatPrice(cart.total) }}
                                <ArrowRight class="ml-2 size-5 transition-transform group-hover:translate-x-1" />
                            </Button>
                        </form>
                    </div>

                    <!-- PayPal Section -->
                    <div v-else key="paypal" class="space-y-8 animate-in fade-in slide-in-from-right-8 duration-500">
                        <div class="bg-primary/5 border border-primary/20 rounded-3xl p-6 flex items-start gap-4">
                            <Info class="size-5 text-primary flex-shrink-0 mt-0.5" />
                            <p class="font-bold text-muted-foreground leading-relaxed text-sm">
                                You will be redirected to PayPal's secure portal to authorize this transaction. Laravel Cart never stores your login credentials.
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <Button 
                                @click="handlePaypalClick"
                                class="w-full h-14 bg-[#ffc439] hover:bg-[#f2ba36] text-[#111] rounded-2xl font-black shadow-lg shadow-[#ffc439]/20 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2"
                            >
                                <span class="text-lg">Checkout with</span>
                                <span class="flex items-center text-xl">
                                    <span class="text-[#003087]">Pay</span><span class="text-[#009cde]">Pal</span>
                                </span>
                            </Button>
                            <Button 
                                @click="handlePaypalClick"
                                variant="outline"
                                class="w-full h-14 rounded-2xl border-muted-foreground/20 font-black text-muted-foreground uppercase tracking-widest text-xs hover:bg-muted transition-all"
                            >
                                Pay with Debit or Credit Card
                            </Button>
                        </div>

                        <Separator class="bg-muted-foreground/10" />

                        <div class="flex items-center justify-center gap-6 opacity-30 grayscale scale-90">
                             <div class="text-[10px] font-black italic">VISA</div>
                             <div class="text-[10px] font-black italic">MASTERCARD</div>
                             <div class="text-[10px] font-black italic">PAYPAL</div>
                             <div class="text-[10px] font-black italic">STRIPE</div>
                        </div>
                    </div>
                </transition>

                <!-- Secure Footer -->
                <div class="pt-8 text-center space-y-4">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-500/5 text-green-600 rounded-full border border-green-500/10">
                        <ShieldCheck class="size-3" />
                        <span class="text-[9px] font-black uppercase tracking-[0.1em]">Verified PCI-DSS Compliant</span>
                    </div>
                    <p class="text-[10px] text-muted-foreground font-medium leading-relaxed max-w-sm mx-auto">
                        Your transaction is processed using end-to-end encryption. Laravel Cart utilizes advanced fraud detection algorithms for every purchase.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.shadow-soft {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
}

.shadow-premium {
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
}
</style>
