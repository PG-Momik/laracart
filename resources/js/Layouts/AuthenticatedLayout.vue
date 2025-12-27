<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';
import { Toaster } from '@/Components/ui/sonner';
import { toast } from 'vue-sonner';
import { 
    DropdownMenu, 
    DropdownMenuContent, 
    DropdownMenuItem, 
    DropdownMenuLabel, 
    DropdownMenuSeparator, 
    DropdownMenuTrigger 
} from '@/Components/ui/dropdown-menu';
import { Button } from '@/Components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle, 
    DialogDescription,
    DialogFooter
} from '@/Components/ui/dialog';
import { 
    ShoppingBag, 
    User, 
    LogOut, 
    LayoutDashboard, 
    Package, 
    History,
    CheckCircle2,
    Menu,
    X,
    Bell
} from 'lucide-vue-next';

const showingNavigationDropdown = ref(false);
const successModalOpen = ref(false);
const successMessage = ref('');
const countdown = ref(10);
let timer = null;

const { fetchCart, cart } = useCart();
const page = usePage();

onMounted(() => {
    fetchCart();
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const startCountdown = () => {
    countdown.value = 10;
    timer = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            clearInterval(timer);
            successModalOpen.value = false;
            router.visit(route('products.index'));
        }
    }, 1000);
};

// Watch for flash messages
watch(() => page.props.flash?.success, (message) => {
    if (message) {
        if (message.toLowerCase().includes('order')) {
            successMessage.value = message;
            successModalOpen.value = true;
            startCountdown();
        } else {
            toast.success(message);
        }
    }
}, { immediate: true });

watch(() => page.props.flash?.error, (message) => {
    if (message) {
        toast.error(message);
    }
}, { immediate: true });

const initials = computed(() => {
    return page.props.auth.user.name.split(' ').map(n => n[0]).join('').toUpperCase();
});

</script>

<template>
    <div class="min-h-screen bg-background font-sans antialiased selection:bg-primary/10 selection:text-primary">
        <Toaster position="top-right" rich-colors close-button />

        <!-- Navigation -->
        <nav class="sticky top-0 z-40 w-full border-b bg-background/80 backdrop-blur-xl transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Section: Logo & Nav -->
                    <div class="flex items-center gap-8">
                        <Link :href="route('dashboard')" class="flex items-center gap-2 group">
                            <div class="size-9 bg-primary rounded-lg flex items-center justify-center shadow-lg shadow-primary/20 group-hover:rotate-12 transition-transform duration-300">
                                <ShoppingBag class="size-5 text-primary-foreground" />
                            </div>
                            <span class="text-xl font-black tracking-tighter text-foreground hidden sm:block">LARAVEL CART</span>
                        </Link>

                        <div class="hidden md:flex items-center gap-1">
                            <Link :href="route('dashboard')">
                                <Button variant="ghost" :class="[route().current('dashboard') ? 'bg-muted text-foreground' : 'text-muted-foreground']" class="rounded-lg font-bold">
                                    <LayoutDashboard class="size-4 mr-2" />
                                    Dashboard
                                </Button>
                            </Link>
                            <Link :href="route('products.index')">
                                <Button variant="ghost" :class="[route().current('products.*') ? 'bg-muted text-foreground' : 'text-muted-foreground']" class="rounded-lg font-bold">
                                    <Package class="size-4 mr-2" />
                                    Marketplace
                                </Button>
                            </Link>
                            <Link :href="route('orders.index')">
                                <Button variant="ghost" :class="[route().current('orders.*') ? 'bg-muted text-foreground' : 'text-muted-foreground']" class="rounded-lg font-bold">
                                    <History class="size-4 mr-2" />
                                    My Orders
                                </Button>
                            </Link>
                        </div>
                    </div>

                    <!-- Right Section: Cart & User -->
                    <div class="flex items-center gap-2">
                        <!-- Notifications -->
                        <Button variant="ghost" size="icon" class="rounded-full text-muted-foreground hover:text-foreground hidden sm:flex">
                            <Bell class="size-5" />
                        </Button>

                        <Link :href="route('cart.index')" class="relative">
                            <Button variant="ghost" size="icon" class="rounded-full text-muted-foreground hover:text-foreground">
                                <ShoppingBag class="size-5" />
                                <span v-if="cart.count > 0" class="absolute -top-1 -right-1 size-5 bg-primary text-[10px] font-black text-primary-foreground rounded-full flex items-center justify-center border-2 border-background animate-in zoom-in-50 duration-300">
                                    {{ cart.count }}
                                </span>
                            </Button>
                        </Link>

                        <div class="h-6 w-px bg-border mx-1 hidden sm:block"></div>

                        <!-- User Profile Dropdown -->
                        <div class="flex items-center">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" class="p-1 space-x-2 rounded-full hover:bg-muted transition-colors">
                                        <div class="text-right mr-2 hidden lg:block">
                                            <p class="text-xs font-black text-foreground leading-none">{{ $page.props.auth.user.name }}</p>
                                            <p class="text-[10px] font-bold text-muted-foreground mt-0.5 leading-none">{{ $page.props.auth.user.email }}</p>
                                        </div>
                                        <Avatar class="size-8 border-2 border-primary/10 transition-transform hover:scale-105">
                                            <AvatarImage src="" />
                                            <AvatarFallback class="bg-primary/5 text-primary text-[10px] font-black">{{ initials }}</AvatarFallback>
                                        </Avatar>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="right" class="w-56 rounded-lg shadow-2xl mt-2 border-muted-foreground/10 p-1">
                                    <DropdownMenuLabel class="px-2 py-1.5 text-xs font-black uppercase tracking-widest text-muted-foreground">Account</DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem class="rounded-lg my-0.5 flex items-center gap-2 font-bold cursor-pointer" @click="router.visit(route('profile.edit'))">
                                        <User class="size-4" /> Profile
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem class="rounded-lg my-0.5 flex items-center gap-2 font-bold text-destructive focus:text-destructive focus:bg-destructive/5 cursor-pointer" @click="router.post(route('logout'))">
                                        <LogOut class="size-4" /> Log Out
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <!-- Mobile Menu Button -->
                        <div class="md:hidden">
                            <Button variant="ghost" size="icon" @click="showingNavigationDropdown = !showingNavigationDropdown" class="rounded-lg">
                                <Menu v-if="!showingNavigationDropdown" class="size-6 transition-transform hover:rotate-90 duration-300" />
                                <X v-else class="size-6 transition-transform rotate-90" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <div v-if="showingNavigationDropdown" class="md:hidden border-b bg-card absolute w-full shadow-2xl rounded-b-3xl">
                    <div class="px-4 py-6 space-y-2">
                        <Link :href="route('dashboard')">
                            <Button variant="ghost" class="w-full justify-start h-12 rounded-lg font-bold" :class="route().current('dashboard') ? 'bg-muted' : ''">
                                <LayoutDashboard class="size-5 mr-3" /> Dashboard
                            </Button>
                        </Link>
                        <Link :href="route('products.index')">
                            <Button variant="ghost" class="w-full justify-start h-12 rounded-lg font-bold" :class="route().current('products.*') ? 'bg-muted' : ''">
                                <Package class="size-5 mr-3" /> Marketplace
                            </Button>
                        </Link>
                        <Link :href="route('orders.index')">
                            <Button variant="ghost" class="w-full justify-start h-12 rounded-lg font-bold" :class="route().current('orders.*') ? 'bg-muted' : ''">
                                <History class="size-5 mr-3" /> My Orders
                            </Button>
                        </Link>
                        <Link :href="route('cart.index')">
                            <Button variant="ghost" class="w-full justify-start h-12 rounded-lg font-bold" :class="route().current('cart.*') ? 'bg-muted' : ''">
                                <ShoppingBag class="size-5 mr-3" /> Cart ({{ cart.count }})
                            </Button>
                        </Link>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-muted/30 pt-16 pb-12 border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-0">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-20 py-12 border-t bg-muted/30">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs font-black text-muted-foreground tracking-[0.3em] uppercase">Laravel Cart &copy; 2024</p>
            </div>
        </footer>

        <!-- Order Success Dialog -->
        <Dialog :open="successModalOpen" @update:open="successModalOpen = $event">
            <DialogContent class="sm:max-w-md rounded-2xl border-none shadow-2xl p-10 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute -top-12 -right-12 size-48 bg-primary/10 rounded-full blur-3xl" />
                
                <div class="flex flex-col items-center text-center">
                    <div class="size-20 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mb-6 animate-bounce">
                        <CheckCircle2 class="size-10 text-green-600 dark:text-green-500" />
                    </div>
                    <DialogHeader>
                        <DialogTitle class="text-3xl font-black text-foreground tracking-tight leading-none mb-2">Order Confirmed!</DialogTitle>
                        <DialogDescription class="text-muted-foreground font-medium text-base px-4">
                            {{ successMessage }}
                        </DialogDescription>
                    </DialogHeader>
                    
                    <div class="mt-8 w-full space-y-4 relative z-10">
                        <div class="flex items-center justify-center gap-3">
                            <div class="h-1.5 w-1.5 rounded-full bg-primary animate-ping" />
                            <span class="text-xs font-bold text-muted-foreground uppercase tracking-widest">
                                Redirecting in <span class="bg-muted px-2 py-0.5 rounded text-primary font-black ml-1 tabular-nums">{{ countdown }}</span>
                            </span>
                        </div>
                        
                        <Button class="w-full h-12 rounded-lg font-bold text-base shadow-lg shadow-primary/20" @click="successModalOpen = false; router.visit(route('products.index'))">
                            Keep Shopping
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style>
/* Custom Scrollbar for better aesthetics */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground) / 0.2);
    border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.4);
}
</style>

