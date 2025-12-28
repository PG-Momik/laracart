<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import ProductCardInlineActions from '@/Components/ProductCardInlineActions.vue';
import {Head, Link, router} from '@inertiajs/vue3';
import {computed, onMounted, onUnmounted, ref, watch} from 'vue';
import axios from 'axios';

// Shadcn UI
import {Input} from '@/Components/ui/input';
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from '@/Components/ui/select';
import {Button} from '@/Components/ui/button';
import {Card} from '@/Components/ui/card';
import {Badge} from '@/Components/ui/badge';
import {ArrowRight, Filter, LayoutGrid, List, Loader2, Package, Search, Star, X} from 'lucide-vue-next';
// Simple native debounce implementation
const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
};

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || 'all');
const layout = ref(props.filters.layout || 'grid');

// Local state for products accumulated during infinite scroll
const allProducts = ref([...props.products.data]);
const nextUrl = ref(props.products.links.next);
const loadingMore = ref(false);
const loadMoreTrigger = ref(null);

// Search Recommendations State
const showRecommendations = ref(false);
const recommendations = ref([]);
const recommendationsNextUrl = ref(null);
const loadingRecommendations = ref(false);
const recommendationsContainer = ref(null);
const recommendationsSentinel = ref(null);

let observer = null;
let recommendationsObserver = null;

const categoriesOptions = computed(() => {
  return ['all', ...props.categories].map(c => ({
    value: c,
    label: c === 'all' ? 'All Categories' : c.charAt(0).toUpperCase() + c.slice(1)
  }));
});

// Watchers for filters
const updateFilters = debounce(() => {
  router.get(
      route('products.index'),
      {
        search: search.value,
        category: category.value,
        layout: layout.value
      },
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: (page) => {
          allProducts.value = [...page.props.products.data];
          nextUrl.value = page.props.products.links.next;
          showRecommendations.value = false;
        }
      }
  );
}, 300);

watch([category, layout], () => {
  updateFilters();
});

// Live Search Recommendations
const fetchRecommendations = debounce((query) => {
  if (!query) {
    recommendations.value = [];
    recommendationsNextUrl.value = null;
    return;
  }

  loadingRecommendations.value = true;
  axios.get(route('products.index'), {
    params: {search: query, per_page: 8, json: true},
    headers: {'X-Requested-With': 'XMLHttpRequest'}
  }).then(response => {
    recommendations.value = response.data.data;
    recommendationsNextUrl.value = response.data.links.next;
    showRecommendations.value = true;
  }).catch(err => console.error(err))
      .finally(() => loadingRecommendations.value = false);
}, 400);

watch(search, (newVal) => {
  if (newVal === '') {
    showRecommendations.value = false;
    updateFilters();
  } else {
    fetchRecommendations(newVal);
  }
});

const loadMoreRecommendations = () => {
  if (loadingRecommendations.value || !recommendationsNextUrl.value) return;

  loadingRecommendations.value = true;
  axios.get(recommendationsNextUrl.value, {
    headers: {'X-Requested-With': 'XMLHttpRequest'}
  })
      .then(response => {
        recommendations.value = [...recommendations.value, ...response.data.data];
        recommendationsNextUrl.value = response.data.links.next;
      })
      .finally(() => loadingRecommendations.value = false);
};

const selectRecommendation = (recommendation) => {
  router.visit(route('products.show', recommendation.id));
};

const loadMore = () => {
  if (loadingMore.value || !nextUrl.value) return;
  loadingMore.value = true;

  router.get(nextUrl.value, {
    search: search.value,
    category: category.value,
    layout: layout.value
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['products'],
    onSuccess: (page) => {
      const newProducts = page.props.products.data;
      allProducts.value = [...allProducts.value, ...newProducts];
      nextUrl.value = page.props.products.links.next;
    },
    onFinish: () => {
      loadingMore.value = false;
    }
  });
};

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && nextUrl.value && !loadingMore.value) {
      loadMore();
    }
  }, {rootMargin: '400px'});

  if (loadMoreTrigger.value) observer.observe(loadMoreTrigger.value);

  recommendationsObserver = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && recommendationsNextUrl.value && !loadingRecommendations.value) {
      loadMoreRecommendations();
    }
  }, {threshold: 0.1});

  const handleClickOutside = (e) => {
    if (recommendationsContainer.value && !recommendationsContainer.value.contains(e.target)) {
      showRecommendations.value = false;
    }
  };
  window.addEventListener('click', handleClickOutside);
  onUnmounted(() => window.removeEventListener('click', handleClickOutside));
});

watch(loadMoreTrigger, (newEl, oldEl) => {
  if (oldEl) observer.unobserve(oldEl);
  if (newEl) observer.observe(newEl);
});

watch(recommendationsSentinel, (newEl, oldEl) => {
  if (oldEl) recommendationsObserver.unobserve(oldEl);
  if (newEl) recommendationsObserver.observe(newEl);
});

onUnmounted(() => {
  if (observer) observer.disconnect();
  if (recommendationsObserver) recommendationsObserver.disconnect();
});

watch(() => props.products.data, (newData) => {
  if (!loadingMore.value) {
    const updatedMap = new Map(newData.map(p => [p.id, p]));
    allProducts.value = allProducts.value.map(existingProduct => {
      if (updatedMap.has(existingProduct.id)) {
        return updatedMap.get(existingProduct.id);
      }
      return existingProduct;
    });
    if (allProducts.value.length === 0 || (allProducts.value.length <= newData.length && allProducts.value.every((p, i) => newData[i] && p.id === newData[i].id))) {
      allProducts.value = [...newData];
    }
    nextUrl.value = props.products.links.next;
  }
}, {deep: true});

</script>

<template>
  <Head title="Browse Products"/>

  <AuthenticatedLayout>
    <template #header>
      <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h2 class="font-black text-3xl text-foreground leading-tight tracking-tight">Marketplace</h2>
            <p class="text-sm text-muted-foreground mt-1 font-medium">Discover our curated collection of products</p>
          </div>

          <div class="flex items-center gap-2 group">
            <Button
                :class="layout === 'grid' ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/20' : 'bg-muted/30 text-muted-foreground hover:bg-muted'"
                class="rounded-lg transition-all duration-300 border-border"
                size="icon"
                variant="outline"
                @click="layout = 'grid'"
            >
              <LayoutGrid class="size-4"/>
            </Button>
            <Button
                :class="layout === 'list' ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/20' : 'bg-muted/30 text-muted-foreground hover:bg-muted'"
                class="rounded-lg transition-all duration-300 border-border"
                size="icon"
                variant="outline"
                @click="layout = 'list'"
            >
              <List class="size-4"/>
            </Button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
          <!-- Search with Recommendations -->
          <div ref="recommendationsContainer" class="md:col-span-8 relative group">
            <div class="relative">
              <Search
                  class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground group-focus-within:text-primary transition-colors"/>
              <Input
                  v-model="search"
                  class="pl-10 h-12 bg-card border-muted-foreground/10 focus-visible:ring-primary shadow-sm rounded-lg text-base"
                  placeholder="Search products..."
                  @focus="showRecommendations = search.length > 0"
              />
              <Button
                  v-if="search"
                  class="absolute right-2 top-1/2 -translate-y-1/2 size-8 text-muted-foreground hover:text-foreground"
                  size="icon"
                  variant="ghost"
                  @click="search = ''"
              >
                <X class="size-4"/>
              </Button>
            </div>

            <!-- Recommendations Dropdown -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-2"
            >
              <Card
                  v-if="showRecommendations && (recommendations.length > 0 || loadingRecommendations)"
                  class="absolute z-[999] mt-2 w-full shadow-premium rounded-md overflow-hidden border-border bg-popover text-popover-foreground"
              >
                <div class="max-h-96 overflow-y-auto p-2 space-y-1">
                  <div
                      v-for="item in recommendations"
                      :key="item.id"
                      class="flex items-center gap-4 p-3 hover:bg-muted/50 cursor-pointer rounded-lg transition-colors group/item"
                      @click="selectRecommendation(item)"
                  >
                    <div
                        class="size-12 rounded-lg overflow-hidden bg-muted flex-shrink-0 border border-muted-foreground/10">
                      <img :src="item.image_url" class="w-full h-full object-contain p-1" decoding="async"
                           loading="eager"/>
                    </div>
                    <div class="flex-grow min-w-0">
                      <p class="text-sm font-bold text-foreground truncate group-hover/item:text-primary transition-colors">
                        {{ item.name }}</p>
                      <div class="flex items-center gap-2 mt-0.5">
                        <Badge class="text-[10px] px-1.5 font-bold uppercase py-0" variant="secondary">{{
                            item.category
                          }}
                        </Badge>
                        <span class="text-sm font-black text-primary">{{ item.formatted_price }}</span>
                      </div>
                    </div>
                    <ArrowRight
                        class="size-4 text-muted-foreground opacity-0 group-hover/item:opacity-100 translate-x-1 transition-all"/>
                  </div>

                  <!-- Dropdown Infinite Scroll Sentinel -->
                  <div ref="recommendationsSentinel"
                       class="py-4 flex items-center justify-center border-t border-muted-foreground/5 mt-2">
                    <div v-if="loadingRecommendations" class="flex items-center gap-2">
                      <Loader2 class="size-4 animate-spin text-primary"/>
                      <span
                          class="text-xs font-semibold text-muted-foreground uppercase tracking-widest">Searching...</span>
                    </div>
                  </div>
                </div>
              </Card>
            </transition>
          </div>

          <!-- Category Filter -->
          <div class="md:col-span-4 flex gap-2">
            <div class="relative w-full">
              <Select v-model="category">
                <SelectTrigger
                    class="h-12 border-muted-foreground/10 bg-card rounded-lg px-4 focus:ring-primary shadow-sm hover:border-primary/50 transition-colors">
                  <Filter class="size-4 mr-2 text-muted-foreground"/>
                  <SelectValue placeholder="All Categories"/>
                </SelectTrigger>
                <SelectContent class="rounded-md shadow-premium border-border bg-popover">
                  <SelectItem v-for="opt in categoriesOptions" :key="opt.value" :value="opt.value"
                              class="rounded-lg my-1">
                    {{ opt.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <!-- Grid Layout -->
      <transition mode="out-in" name="fade">
        <div v-if="layout === 'grid'" key="grid">
          <div v-if="allProducts.length > 0"
               class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <ProductCard
                v-for="product in allProducts"
                :key="product.id"
                :product="product"
            />
          </div>

          <div v-else class="text-center py-24 bg-card rounded-xl border border-dashed border-muted-foreground/20">
            <Package class="size-16 text-muted-foreground/30 mx-auto mb-4"/>
            <h3 class="text-xl font-bold text-foreground">No products found</h3>
            <p class="text-muted-foreground mt-2 max-w-xs mx-auto">Try adjusting your filters or search terms to find
              what you're looking for.</p>
            <Button class="mt-6 rounded-lg" variant="outline" @click="search = ''; category = 'all'">
              Reset Filters
            </Button>
          </div>

          </div>

        <!-- List Layout -->
        <div v-else key="list">
          <div v-if="allProducts.length > 0" class="flex flex-col gap-6">
            <Card v-for="product in allProducts" :key="product.id"
                  class="group p-5 border-none shadow-soft hover:shadow-hover transition-all duration-500 rounded-md bg-card overflow-hidden">
              <div class="flex flex-col sm:flex-row gap-8 items-center sm:items-stretch">
                <div
                    class="w-full sm:w-48 h-48 flex-shrink-0 bg-muted/30 rounded-lg overflow-hidden group-hover:scale-[1.02] transition-transform duration-500 relative">
                  <img :alt="product.name" :src="product.image_url" class="w-full h-full object-contain p-4" decoding="async"
                       loading="eager"/>
                </div>

                <div class="flex-grow flex flex-col pt-2">
                  <div class="flex justify-between items-start mb-3">
                    <div>
                      <Link :href="route('products.show', product.id)">
                        <h3 class="text-2xl font-black text-foreground hover:text-primary transition-colors mb-1">
                          {{ product.name }}</h3>
                      </Link>
                      <div class="flex flex-wrap gap-2">
                                                <span v-for="tag in product.tags" :key="tag"
                                                      class="text-[10px] font-black uppercase text-primary tracking-widest underline decoration-2 decoration-primary/20 underline-offset-4">
                                                    #{{ tag }}
                                                </span>
                      </div>
                    </div>
                    <div class="text-2xl font-black text-primary bg-primary/5 px-4 py-2 rounded-lg">
                      {{ product.formatted_price }}
                    </div>
                  </div>

                  <p class="text-sm text-muted-foreground leading-relaxed line-clamp-2 max-w-2xl">
                    {{ product.description }}
                  </p>

                  <div class="mt-auto pt-6 border-t border-muted-foreground/5">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6 mb-4">
                      <div class="flex items-center gap-6">
                        <div
                            class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-muted-foreground/60">
                          <span>{{ product.stock_status.label }}</span>
                          <span>&bull;</span>
                          <span>{{ product.stock_quantity }} units available</span>
                        </div>
                        <div class="flex items-center gap-1">
                          <Star class="size-3 fill-yellow-400 text-yellow-400"/>
                          <span class="text-xs font-black text-foreground">4.8</span>
                        </div>
                      </div>

                      <div class="flex items-center gap-3">
                        <Link :href="route('products.show', product.id)">
                          <Button class="rounded-lg font-bold hover:bg-primary/5 hover:text-primary group/btn"
                                  variant="ghost">
                            Full Details
                            <ArrowRight class="size-4 ml-2 group-hover/btn:translate-x-1 transition-transform"/>
                          </Button>
                        </Link>
                        <ProductCardInlineActions :product="product"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </Card>
          </div>

          <div v-else class="text-center py-24 bg-card rounded-xl border border-dashed border-muted-foreground/20">
            <Package class="size-16 text-muted-foreground/30 mx-auto mb-4"/>
            <h3 class="text-xl font-bold text-foreground">No products found</h3>
            <p class="text-muted-foreground mt-2">Try adjusting your filters or search terms.</p>
          </div>
        </div>
      </transition>

      <!-- Infinite Scroll Trigger (Universal) -->
      <div v-if="nextUrl" ref="loadMoreTrigger" class="py-16 flex justify-center">
        <div v-if="loadingMore" class="flex flex-col items-center gap-4">
          <div class="flex items-center gap-1.5">
            <div class="size-2 rounded-full bg-primary animate-bounce delay-0"></div>
            <div class="size-2 rounded-full bg-primary animate-bounce delay-150"></div>
            <div class="size-2 rounded-full bg-primary animate-bounce delay-300"></div>
          </div>
          <span class="text-xs font-black text-muted-foreground uppercase tracking-[0.2em]">Loading Inventory</span>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.shadow-soft {
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
}

.shadow-hover {
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.05);
}

.blink {
  animation: blink 1.5s infinite;
}

@keyframes blink {
  0%, 100% {
    opacity: 1;
    scale: 1;
  }
  50% {
    opacity: 0.5;
    scale: 0.8;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
