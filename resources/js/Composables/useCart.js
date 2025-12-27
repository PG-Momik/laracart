import { reactive, computed, ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

// Global state shared across components
const state = reactive({
    items: [],
    total: 0,
    count: 0,
});

export function useCart() {
    const processing = ref(false);
    const adding = ref(false);

    const fetchCart = async () => {
        try {
            const response = await axios.get('/api/cart');
            state.items = response.data.items;
            state.total = response.data.total;
            state.count = state.items.reduce((acc, item) => acc + item.quantity, 0);
        } catch (error) {
            console.error('Failed to fetch cart:', error);
        }
    };

    const addToCart = async (productOrId, quantity = 1) => {
        const productId = typeof productOrId === 'object' ? productOrId.id : productOrId;

        adding.value = true;
        processing.value = true;
        try {
            const response = await axios.post('/api/cart', {
                product_id: productId,
                quantity: quantity
            });
            await fetchCart();
            // Refresh product data to update stock counts
            router.reload({ only: ['products', 'product'] });
            return response.data.item.product;
        } catch (error) {
            console.error('Failed to add to cart:', error);
            throw error;
        } finally {
            adding.value = false;
            processing.value = false;
        }
    };

    const updateQuantity = async (itemId, quantity) => {
        processing.value = true;
        try {
            await axios.put(`/api/cart/${itemId}`, {
                quantity: quantity
            });
            await fetchCart();
            router.reload({ only: ['products', 'product'] });
        } catch (error) {
            console.error('Failed to update cart:', error);
            throw error;
        } finally {
            processing.value = false;
        }
    };

    const removeFromCart = async (itemId) => {
        processing.value = true;
        try {
            await axios.delete(`/api/cart/${itemId}`);
            await fetchCart();
            router.reload({ only: ['products', 'product'] });
        } catch (error) {
            console.error('Failed to remove item:', error);
        } finally {
            processing.value = false;
        }
    };

    return {
        cart: computed(() => state),
        adding,
        processing,
        fetchCart,
        addToCart,
        updateQuantity,
        removeFromCart
    };
}
