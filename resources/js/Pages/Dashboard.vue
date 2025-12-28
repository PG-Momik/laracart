<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {onMounted, ref} from 'vue';
import {Chart, registerables} from 'chart.js';
import {AlertCircle, History, LayoutDashboard, Mail, Play, RefreshCcw, Terminal, Zap} from 'lucide-vue-next';

// Shadcn UI
import {Button} from '@/Components/ui/button';
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from '@/Components/ui/card';
import {Input} from '@/Components/ui/input';
import {Label} from '@/Components/ui/label';
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from '@/Components/ui/select';

Chart.register(...registerables);

const props = defineProps({
  stats: Object,
  charts: Object,
  products: Array,
});

const categoryChartRef = ref(null);
const stockChartRef = ref(null);

onMounted(() => {
  // Category Chart
  new Chart(categoryChartRef.value, {
    type: 'bar',
    data: {
      labels: props.charts.categories.labels,
      datasets: [{
        label: 'Products',
        data: props.charts.categories.data,
        backgroundColor: 'rgba(99, 102, 241, 0.6)', // Indigo
        borderColor: 'rgb(99, 102, 241)',
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {legend: {display: false}},
      scales: {
        y: {
          beginAtZero: true,
          grid: {color: 'rgba(255,255,255,0.05)'},
          ticks: {color: 'rgba(255,255,255,0.5)'}
        },
        x: {
          grid: {display: false},
          ticks: {color: 'rgba(255,255,255,0.5)'}
        }
      }
    }
  });

  // Stock Distribution
  new Chart(stockChartRef.value, {
    type: 'doughnut',
    data: {
      labels: props.charts.stock_status.labels,
      datasets: [{
        data: props.charts.stock_status.data,
        backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'], // Green, Amber, Red
        borderWidth: 0,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {legend: {position: 'bottom'}}
    }
  });
});

// Command Control Logic
const selectedCommand = ref('products:fetch');
const commandForm = useForm({
  command: 'products:fetch',
  email: '',
  params: {
    '--limit': '50',
    'product_id': '',
    'quantity': '10',
    '--status': 'LOW STOCK'
  }
});

const availableCommands = [
  {value: 'products:fetch', label: 'Fetch Dummy Products', icon: RefreshCcw, desc: 'Sync from DummyJSON API'},
  {value: 'app:send-restock-mail', label: 'Send Restock Mail', icon: Mail, desc: 'Alert consumer of restock'},
  {value: 'app:send-stock-alert', label: 'Send Stock Alert', icon: AlertCircle, desc: 'Alert admin of low stock'},
  {value: 'app:send-daily-report', label: 'Send Daily Report', icon: History, desc: 'Daily inventory summary'}
];

const runCommand = () => {
  let params = {};
  if (selectedCommand.value === 'products:fetch') {
    params = {'--limit': commandForm.params['--limit']};
  } else if (selectedCommand.value === 'app:send-restock-mail') {
    params = {
      'product_id': commandForm.params.product_id,
      'quantity': commandForm.params.quantity
    };
  } else if (selectedCommand.value === 'app:send-stock-alert') {
    params = {
      'product_id': commandForm.params.product_id,
      '--status': commandForm.params['--status']
    };
  } else if (selectedCommand.value === 'app:send-daily-report') {
    params = {};
  }

  commandForm.transform((data) => ({
    ...data,
    command: selectedCommand.value,
    params: params
  })).post(route('dashboard.command'), {
    preserveScroll: true,
  });
};

const formatValue = (value) => {
  if (value >= 1000000) {
    return '$' + (value / 1000000).toFixed(1) + 'M';
  }
  if (value >= 1000) {
    return '$' + (value / 1000).toFixed(1) + 'k';
  }
  return '$' + value.toLocaleString();
};
</script>

<template>
  <Head title="Dashboard"/>

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <div class="size-12 bg-primary/10 rounded-xl flex items-center justify-center border border-primary/20">
          <LayoutDashboard class="size-6 text-primary"/>
        </div>
        <div>
          <h2 class="text-2xl font-black text-foreground tracking-tighter uppercase italic">Dashboard</h2>
          <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-[0.2em]">Administrative Command &
            Intelligence</p>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Control Column (Artisan Commands) -->
        <div class="lg:col-span-4 space-y-6">
          <Card
              class="border-none shadow-premium bg-card/50 backdrop-blur-xl rounded-2xl overflow-hidden border-t border-white/5">
            <CardHeader class="p-8">
              <CardTitle class="text-sm font-black uppercase tracking-widest text-primary flex items-center gap-2">
                <Terminal class="size-4"/>
                Artisan Console
              </CardTitle>
              <CardDescription class="text-[10px] uppercase font-bold text-muted-foreground mt-1">Execute specialized
                system operations
              </CardDescription>
            </CardHeader>
            <CardContent class="p-8 pt-0 space-y-6">
              <div class="space-y-4">
                <div class="space-y-2">
                  <Label class="text-[10px] font-black uppercase tracking-wider opacity-50">Select Operation</Label>
                  <Select v-model="selectedCommand">
                    <SelectTrigger class="h-12 rounded-md bg-muted/30 border-none font-bold">
                      <SelectValue placeholder="Select command"/>
                    </SelectTrigger>
                    <SelectContent class="rounded-md border-border">
                      <SelectItem v-for="cmd in availableCommands" :key="cmd.value" :value="cmd.value"
                                  class="rounded-lg font-bold">
                        <div class="flex items-center gap-2">
                          <component :is="cmd.icon" class="size-3.5"/>
                          {{ cmd.label }}
                        </div>
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <!-- Email Recipient (Global for mailing commands) -->
                <div
                    v-if="['app:send-restock-mail', 'app:send-stock-alert', 'app:send-daily-report'].includes(selectedCommand)"
                    class="space-y-2 animate-in fade-in slide-in-from-top-2 duration-300">
                  <Label class="text-[10px] font-black uppercase tracking-wider opacity-50 flex items-center gap-1">
                    Recipient Email
                    <span class="text-destructive font-black text-xs">*</span>
                  </Label>
                  <Input
                      v-model="commandForm.email"
                      :class="[
                                            'h-12 rounded-md bg-muted/30 border-none font-bold placeholder:opacity-50 transition-all',
                                            commandForm.errors.email ? 'ring-2 ring-destructive' : 'focus:ring-2 focus:ring-primary'
                                        ]"
                      placeholder="admin@example.com"
                      type="email"
                  />
                  <p v-if="commandForm.errors.email"
                     class="text-[10px] font-bold text-destructive uppercase animate-in fade-in slide-in-from-top-1">
                    {{ commandForm.errors.email }}</p>
                </div>

                <!-- Dynamic Parameters -->
                <div v-if="selectedCommand === 'products:fetch'"
                     class="space-y-4 animate-in fade-in slide-in-from-top-2 duration-300">
                  <div class="space-y-2">
                    <Label class="text-[10px] font-black uppercase tracking-wider opacity-50">Fetch Limit</Label>
                    <Input v-model="commandForm.params['--limit']" class="h-12 rounded-md bg-muted/30 border-none font-bold no-spinner"
                           type="number"/>
                    <p v-if="commandForm.errors['params.--limit']"
                       class="text-[10px] font-bold text-destructive uppercase">{{
                        commandForm.errors['params.--limit']
                      }}</p>
                  </div>
                </div>

                <div v-if="selectedCommand === 'app:send-restock-mail' || selectedCommand === 'app:send-stock-alert'"
                     class="space-y-4 animate-in fade-in slide-in-from-top-2 duration-300">
                  <div class="space-y-2">
                    <Label class="text-[10px] font-black uppercase tracking-wider opacity-50">Target Product</Label>
                    <Select v-model="commandForm.params.product_id">
                      <SelectTrigger
                          :class="['h-12 rounded-md bg-muted/30 border-none font-bold', commandForm.errors['params.product_id'] && 'ring-2 ring-destructive']">
                        <SelectValue placeholder="Select a product"/>
                      </SelectTrigger>
                      <SelectContent class="rounded-md border-border">
                        <SelectItem v-for="product in products" :key="product.id" :value="product.id.toString()"
                                    class="rounded-lg font-bold">
                          {{ product.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                    <p v-if="commandForm.errors['params.product_id']"
                       class="text-[10px] font-bold text-destructive uppercase">
                      {{ commandForm.errors['params.product_id'] }}</p>
                  </div>

                  <div v-if="selectedCommand === 'app:send-restock-mail'" class="space-y-2">
                    <Label class="text-[10px] font-black uppercase tracking-wider opacity-50">Restock Quantity</Label>
                    <Input v-model="commandForm.params.quantity" class="h-12 rounded-md bg-muted/30 border-none font-bold no-spinner"
                           type="number"/>
                    <p v-if="commandForm.errors['params.quantity']"
                       class="text-[10px] font-bold text-destructive uppercase">{{
                        commandForm.errors['params.quantity']
                      }}</p>
                  </div>

                  <div v-if="selectedCommand === 'app:send-stock-alert'" class="space-y-2">
                    <Label class="text-[10px] font-black uppercase tracking-wider opacity-50">Alert Status</Label>
                    <Input v-model="commandForm.params['--status']"
                           class="h-12 rounded-md bg-muted/30 border-none font-bold"/>
                    <p v-if="commandForm.errors['params.--status']"
                       class="text-[10px] font-bold text-destructive uppercase">{{
                        commandForm.errors['params.--status']
                      }}</p>
                  </div>
                </div>

                <Button
                    :disabled="commandForm.processing"
                    class="w-full h-14 rounded-md font-black uppercase tracking-[0.2em] text-xs gap-3 shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95 group"
                    @click="runCommand"
                >
                  <Play v-if="!commandForm.processing" class="size-4 fill-current group-hover:translate-x-0.5"/>
                  <Zap v-else class="size-4 animate-pulse text-yellow-500"/>
                  {{ commandForm.processing ? 'Executing...' : 'Run Operation' }}
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Right: Intelligence Column (Stats & Charts) -->
        <div class="lg:col-span-8 space-y-8">
          <!-- Stat Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <Card class="border-none shadow-soft bg-card/40 backdrop-blur-sm p-6 space-y-2 rounded-xl">
              <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Total</span>
              <div class="text-3xl font-black text-foreground tracking-tighter">{{ stats.total_products }}</div>
            </Card>
            <Card class="border-none shadow-soft bg-card/40 backdrop-blur-sm p-6 space-y-2 rounded-xl">
              <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Asset Value</span>
              <div class="text-3xl font-black text-primary tracking-tighter">{{
                  formatValue(stats.total_inventory_value)
                }}
              </div>
            </Card>
            <Card
                class="border-none shadow-soft bg-card/40 backdrop-blur-sm p-6 space-y-2 rounded-xl border-l-4 border-l-yellow-600/40">
              <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Low Stock</span>
              <div class="text-3xl font-black text-yellow-600 tracking-tighter">{{ stats.low_stock }}</div>
            </Card>
            <Card
                class="border-none shadow-soft bg-card/40 backdrop-blur-sm p-6 space-y-2 rounded-xl border-l-4 border-l-destructive/40">
              <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Out Stock</span>
              <div class="text-3xl font-black text-destructive tracking-tighter">{{ stats.out_of_stock }}</div>
            </Card>
          </div>

          <!-- Charts Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <Card class="border-none shadow-premium bg-card/50 backdrop-blur-xl rounded-2xl overflow-hidden p-8">
              <div class="flex flex-col gap-1 mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-foreground">Taxonomy</h3>
                <p class="text-[10px] font-bold text-muted-foreground uppercase">Revenue distribution by category</p>
              </div>
              <div class="h-64">
                <canvas ref="categoryChartRef"></canvas>
              </div>
            </Card>

            <Card class="border-none shadow-premium bg-card/50 backdrop-blur-xl rounded-2xl overflow-hidden p-8">
              <div class="flex flex-col gap-1 mb-6">
                <h3 class="text-sm font-black uppercase tracking-wider text-foreground">Surveillance</h3>
                <p class="text-[10px] font-bold text-muted-foreground uppercase">Stock health & availability</p>
              </div>
              <div class="h-64 relative">
                <canvas ref="stockChartRef"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                  <span class="text-3xl font-black text-foreground">{{ stats.total_products }}</span>
                  <span class="text-[8px] uppercase tracking-widest font-black text-muted-foreground">Total Units</span>
                </div>
              </div>
            </Card>
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

/* Remove number input arrows */
.no-spinner::-webkit-inner-spin-button,
.no-spinner::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.no-spinner {
  -moz-appearance: textfield;
}
</style>
