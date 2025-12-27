<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    order: Object,
});
</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Order Details</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold">Order #{{ order.id }}</h3>
                            <Link :href="route('orders.index')" class="text-sm text-gray-500 hover:text-gray-700">
                                &larr; Back to History
                            </Link>
                        </div>
                        
                        <div class="mb-6">
                            <p><strong>Status:</strong> {{ order.status }}</p>
                            <p><strong>Total:</strong> {{ order.total_amount }}</p>
                            <p><strong>Date:</strong> {{ new Date(order.created_at).toLocaleString() }}</p>
                        </div>

                        <h4 class="font-semibold mb-2">Items</h4>
                        <ul class="list-disc pl-5">
                            <li v-for="item in order.items" :key="item.id">
                                {{ item.product.name }} - Qty: {{ item.quantity }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
