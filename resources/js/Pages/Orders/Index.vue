<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    orders: Array,
});
</script>

<template>
    <Head title="Order History" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Order History</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Your Orders</h3>
                        
                        <div v-if="orders.length === 0" class="text-gray-500">
                            No orders found.
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="order in orders" :key="order.id" class="border p-4 rounded bg-gray-50 dark:bg-gray-700">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <strong>Order #{{ order.id }}</strong><br>
                                        <span class="text-sm">Total: {{ order.total_amount }}</span>
                                    </div>
                                    <Link :href="route('orders.show', order.id)" class="text-blue-500 hover:underline">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
