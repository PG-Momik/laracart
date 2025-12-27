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
    ShoppingCart, 
    User, 
    LogOut, 
    LayoutDashboard, 
    Package, 
    History,
    CheckCircle2,
    Menu,
    X,
    Activity,
    Shield,
    ShieldCheck,
    Sun,
    Moon
} from 'lucide-vue-next';
import { useTheme } from '@/Composables/useTheme';

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

const isAdmin = computed(() => {
    const is_admin = page.props.auth.user?.is_admin;
    return is_admin === true || is_admin === 1 || is_admin === '1';
});

const togglePersona = () => {
    router.post(route('profile.toggle-persona'), {}, {
        preserveScroll: true,
    });
};

const { isDark, initTheme, toggleTheme } = useTheme();

onMounted(() => {
    fetchCart();
    initTheme();
});

</script>

<template>
    <div class="min-h-screen bg-background font-sans antialiased selection:bg-primary/10 selection:text-primary">
        <Toaster 
            :theme="isDark ? 'dark' : 'light'" 
            position="bottom-right" 
            rich-colors 
            close-button 
        />

        <!-- Navigation -->
        <nav class="sticky top-0 z-40 w-full border-b bg-background/80 backdrop-blur-xl transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Section: Logo & Nav -->
                    <div class="flex items-center gap-8">
                        <Link :href="isAdmin ? route('dashboard') : route('products.index')" class="flex items-center gap-2 group">
                            <span class="text-xl font-black tracking-tighter text-foreground hidden sm:block">Lara Cart</span>
                        </Link>

                        <div class="hidden md:flex items-center gap-1">
                            <!-- Admin View -->
                            <template v-if="isAdmin">
                                <Link :href="route('dashboard')">
                                    <Button variant="ghost" :class="[route().current('dashboard') ? 'bg-muted text-foreground' : 'text-muted-foreground']" class="rounded-lg font-bold">
                                        <LayoutDashboard class="size-4 mr-2" />
                                        Dashboard
                                    </Button>
                                </Link>
                            </template>

                            <!-- Customer View -->
                            <template v-else>
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
                            </template>
                        </div>
                    </div>

                    <!-- Right Section: Cart & User -->
                    <div class="flex items-center gap-2">
                        <!-- Persona Switcher & Theme Toggle Swap -->
                        <div class="hidden lg:flex items-center gap-2 mr-2">
                            <Button 
                                variant="outline" 
                                size="sm" 
                                @click="togglePersona"
                                :class="[isAdmin ? 'border-primary/50 bg-primary/20 text-primary' : 'text-muted-foreground']"
                                class="rounded-full font-black text-[10px] uppercase tracking-wider gap-2 h-8 px-4 border-dashed"
                            >
                                <component :is="isAdmin ? ShieldCheck : Shield" class="size-3" />
                                {{ isAdmin ? 'Admin Mode' : 'Consumer Mode' }}
                            </Button>

                            <Button
                                variant="ghost"
                                size="icon"
                                @click="toggleTheme"
                                class="rounded-lg text-muted-foreground hover:text-foreground"
                            >
                                <Sun v-if="isDark" class="size-5" />
                                <Moon v-else class="size-5" />
                            </Button>
                        </div>


                        <Link v-if="!isAdmin" :href="route('cart.index')" class="relative">
                            <Button variant="ghost" size="icon" class="rounded-full text-muted-foreground hover:text-foreground">
                                <ShoppingCart class="size-5" />
                                <span :key="cart.count" v-if="cart.count > 0" class="absolute -top-1 -right-1 size-5 bg-primary text-[10px] font-black text-primary-foreground rounded-full flex items-center justify-center border-2 border-background animate-in zoom-in-50 duration-300">
                                    {{ cart.count }}
                                </span>
                            </Button>
                        </Link>

                        <div class="h-6 w-px bg-border mx-1 hidden sm:block"></div>

                        <!-- User Profile Dropdown -->
                        <div class="flex items-center">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" class="p-1 rounded-full hover:bg-muted transition-colors">
                                        <Avatar class="size-8 border-2 border-border transition-transform hover:scale-105">
                                            <AvatarImage src="" />
                                            <AvatarFallback class="bg-muted text-foreground text-[10px] font-black">{{ initials }}</AvatarFallback>
                                        </Avatar>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="right" class="w-56 rounded-md shadow-premium mt-2 border-border p-1">
                                    <div class="px-2 py-2 flex flex-col">
                                        <p class="text-xs font-black text-foreground truncate">{{ $page.props.auth.user.name }}</p>
                                        <p class="text-[10px] font-bold text-muted-foreground truncate">{{ $page.props.auth.user.email }}</p>
                                    </div>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuLabel class="px-2 py-1.5 text-xs font-black uppercase tracking-widest text-muted-foreground">Account</DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem class="rounded-lg my-0.5 flex items-center gap-2 font-bold cursor-pointer text-popover-foreground" @click="router.visit(route('profile.edit'))">
                                        <User class="size-4" /> Profile
                                    </DropdownMenuItem>
                                    <DropdownMenuItem v-if="isAdmin" as-child class="rounded-lg my-0.5 flex items-center gap-2 font-bold cursor-pointer text-popover-foreground">
                                        <a href="/horizon" target="_blank" class="flex items-center gap-2">
                                            <Activity class="size-4" /> Horizon
                                        </a>
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
                        <!-- Admin Mobile -->
                        <template v-if="isAdmin">
                            <Link :href="route('dashboard')">
                                <Button variant="ghost" class="w-full justify-start h-12 rounded-lg font-bold" :class="route().current('dashboard') ? 'bg-muted' : ''">
                                    <LayoutDashboard class="size-5 mr-3" /> Dashboard
                                </Button>
                            </Link>
                        </template>

                        <!-- Consumer Mobile -->
                        <template v-else>
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
                                    <ShoppingCart class="size-5 mr-3" /> Cart ({{ cart.count }})
                                </Button>
                            </Link>
                        </template>
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
        <footer class="mt-20 py-12 border-t">
            <div class="max-w-7xl mx-auto px-4">
                <p class="text-sm font-mono text-muted-foreground">&copy; 2025 Momik Shrestha. Built with purpose.</p>
            </div>
        </footer>
        <!-- Order Success Dialog -->
        <Dialog :open="successModalOpen" @update:open="successModalOpen = $event">
            <DialogContent class="sm:max-w-md rounded-xl border-none shadow-2xl p-10 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute -top-12 -right-12 size-48 bg-primary/10 rounded-full blur-3xl" />
                
                <div class="flex flex-col items-center text-center">
                    <div class="size-20 bg-primary/10 rounded-xl flex items-center justify-center mb-6 animate-bounce">
                        <CheckCircle2 class="size-10 text-primary" />
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
/* Force Sonner Toaster into position if global styles fail to load or get overridden */
[data-sonner-toaster] {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    z-index: 10000 !important;
}

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

