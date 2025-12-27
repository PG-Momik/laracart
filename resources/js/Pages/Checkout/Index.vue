<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    cart: Object,
});

const form = useForm({});

const submit = () => {
    form.post(route('checkout.store'));
};
</script>

<template>
    <Head title="Checkout" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Checkout</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Checkout Page (Commit #8)</h3>
                        <p>Total to Pay: {{ cart.total }}</p>
                        
                        <form @submit.prevent="submit" class="mt-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold" :disabled="form.processing">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
