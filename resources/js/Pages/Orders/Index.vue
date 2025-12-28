<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {CheckCircle2, ChevronRight, Clock, ExternalLink, Package, ShoppingBag, Truck} from 'lucide-vue-next';

// Shadcn UI
import {Button} from '@/Components/ui/button';
import {Card, CardContent, CardHeader} from '@/Components/ui/card';
import {Badge} from '@/Components/ui/badge';
import {Avatar, AvatarFallback, AvatarImage} from '@/Components/ui/avatar';
import {Separator} from '@/Components/ui/separator';

defineProps({
  orders: Object, // Collection
});

const getStatusVariant = (color) => {
  const map = {
    gray: 'secondary',
    blue: 'default',
    green: 'outline', // we will use custom classes for colors
    red: 'destructive',
  };
  return map[color] || 'secondary';
};

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
  <Head title="My Orders"/>

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-3xl font-black text-foreground leading-tight tracking-tight">My Orders</h2>
          <p class="text-sm font-medium text-muted-foreground mt-1">Track and manage your orders</p>
        </div>
      </div>
    </template>

    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div v-if="orders.data.length > 0" class="space-y-8">
        <Card
            v-for="order in orders.data"
            :key="order.id"
            class="group border-none shadow-soft hover:shadow-premium bg-card/50 backdrop-blur-sm transition-all duration-500 overflow-hidden"
        >
          <CardHeader class="p-6 sm:p-8 pb-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
              <div class="flex items-center gap-5">
                <div
                    class="size-16 bg-muted/30 rounded-md flex items-center justify-center border border-muted-foreground/5 group-hover:bg-primary/5 group-hover:border-primary/10 transition-colors duration-500">
                  <Package
                      class="size-7 text-muted-foreground group-hover:text-primary transition-colors duration-500"/>
                </div>
                <div class="space-y-1">
                  <h3 class="text-xl font-black text-foreground tracking-tight">Order #{{ order.id }}</h3>
                  <div class="flex items-center gap-2">
                    <Clock class="size-3.5 text-muted-foreground"/>
                    <p class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">
                      Placed on {{ order.formatted_date }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-4">
                <div class="text-right hidden sm:block">
                  <p class="text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em] mb-1">Total</p>
                  <p class="text-2xl font-black text-primary tracking-tighter">{{ order.formatted_total }}</p>
                </div>
                <Badge
                    :class="['px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border-2 shadow-sm', getStatusClasses(order.status.color)]"
                    :variant="getStatusVariant(order.status.color)"
                >
                  <span
                      :class="['size-1.5 rounded-full mr-2 animate-pulse', order.status.color === 'green' ? 'bg-green-500' : (order.status.color === 'blue' ? 'bg-primary' : 'bg-muted-foreground')]"></span>
                  {{ order.status.label }}
                </Badge>
              </div>
            </div>
          </CardHeader>

          <CardContent class="p-6 sm:p-8 pt-8">
            <Separator class="bg-muted-foreground/10 mb-8"/>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-8">
              <!-- Items Preview -->
              <div v-if="order.items_preview" class="flex items-center gap-3">
                <div class="flex -space-x-4">
                  <div v-for="(item, idx) in order.items_preview" :key="idx" class="relative group/avatar">
                    <Avatar
                        class="size-14 border-4 border-card rounded-md group-hover:translate-y-[-4px] transition-transform duration-300 shadow-soft">
                      <AvatarImage :src="item.image_url" class="object-contain p-2 bg-muted/50"/>
                      <AvatarFallback>{{ item.name.substring(0, 2) }}</AvatarFallback>
                    </Avatar>
                  </div>
                </div>
                <div v-if="order.item_count > order.items_preview.length" class="pl-2">
                  <Badge class="h-14 px-4 rounded-md font-black text-muted-foreground border-dashed border-2"
                         variant="secondary">
                    +{{ order.item_count - order.items_preview.length }} MORE
                  </Badge>
                </div>
              </div>

              <div class="flex items-center gap-3 w-full sm:w-auto">
                <Link :href="route('orders.show', order.id)" class="w-full sm:w-auto">
                  <Button class="w-full h-14 rounded-md font-black text-xs uppercase tracking-widest gap-2 bg-background/50 hover:bg-primary hover:text-primary-foreground transition-all duration-300 border-muted-foreground/10" size="lg"
                          variant="outline">
                    Review Details
                    <ExternalLink class="size-4"/>
                  </Button>
                </Link>
              </div>
            </div>
          </CardContent>

          <div
              class="bg-muted/30 px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-muted-foreground/5">
            <div class="flex items-center gap-2">
              <CheckCircle2 class="size-4 text-green-500"/>
              <span
                  class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Verified Transaction</span>
            </div>
            <div class="flex items-center gap-2 opacity-60">
              <Truck class="size-4 text-muted-foreground"/>
              <p class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Processing order</p>
            </div>
          </div>
        </Card>
      </div>

      <!-- Empty State -->
      <div v-else
           class="text-center py-24 bg-card/40 rounded-2xl border-2 border-dashed border-muted-foreground/20 animate-in fade-in slide-in-from-bottom-8 duration-1000">
        <div class="size-24 bg-muted/20 rounded-xl flex items-center justify-center mb-8 mx-auto">
          <ShoppingBag class="size-12 text-muted-foreground/40 stroke-[1.5]"/>
        </div>
        <h3 class="text-3xl font-black text-foreground tracking-tighter mb-4">No orders found</h3>
        <p class="text-muted-foreground max-w-sm mx-auto mb-10 font-medium">Your My Orders is currently empty. Start
          shopping by exploring our selection.</p>
        <Link :href="route('products.index')">
          <Button class="h-16 px-12 rounded-md font-black text-lg bg-primary shadow-2xl shadow-primary/20 hover:scale-105 active:scale-95 transition-all group"
                  size="lg">
            Enter Marketplace
            <ChevronRight class="ml-2 size-5 transition-transform group-hover:translate-x-1"/>
          </Button>
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.shadow-soft {
  box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
}

.shadow-premium {
  box-shadow: 0 40px 80px -20px rgba(50, 50, 93, 0.1), 0 20px 40px -30px rgba(0, 0, 0, 0.15);
}
</style>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}

.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
