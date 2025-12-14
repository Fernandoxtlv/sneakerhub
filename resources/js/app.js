import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global Alpine Data
Alpine.data('cart', () => ({
    count: 0,
    loading: false,

    async init() {
        await this.fetchCount();
    },

    async fetchCount() {
        try {
            const response = await fetch('/cart/count');
            const data = await response.json();
            this.count = data.count;
        } catch (error) {
            console.error('Error fetching cart count:', error);
        }
    },

    async addToCart(productId, quantity = 1, size = null) {
        this.loading = true;
        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity, size }),
            });
            const data = await response.json();

            if (data.success) {
                this.count = data.cart_count;
                this.showToast('Producto agregado al carrito', 'success');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showToast('Error al agregar al carrito', 'error');
        }
        this.loading = false;
    },

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} animate-slide-up`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}));

Alpine.data('productGallery', () => ({
    activeIndex: 0,
    lightboxOpen: false,

    setActive(index) {
        this.activeIndex = index;
    },

    openLightbox(index = null) {
        if (index !== null) this.activeIndex = index;
        this.lightboxOpen = true;
        document.body.style.overflow = 'hidden';
    },

    closeLightbox() {
        this.lightboxOpen = false;
        document.body.style.overflow = '';
    },

    next() {
        this.activeIndex = (this.activeIndex + 1) % this.images.length;
    },

    prev() {
        this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
    }
}));

Alpine.data('sizeSelector', () => ({
    selectedSize: null,

    select(size) {
        this.selectedSize = size;
    },

    isSelected(size) {
        return this.selectedSize === size;
    }
}));

Alpine.data('quantitySelector', (initial = 1, max = 10) => ({
    quantity: initial,
    max: max,

    increment() {
        if (this.quantity < this.max) this.quantity++;
    },

    decrement() {
        if (this.quantity > 1) this.quantity--;
    }
}));

Alpine.data('filters', () => ({
    isOpen: false,

    toggle() {
        this.isOpen = !this.isOpen;
    }
}));

Alpine.data('dropdown', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    }
}));

Alpine.data('modal', () => ({
    show: false,

    open() {
        this.show = true;
        document.body.style.overflow = 'hidden';
    },

    close() {
        this.show = false;
        document.body.style.overflow = '';
    }
}));

Alpine.data('tabs', (defaultTab = '') => ({
    activeTab: defaultTab,

    isActive(tab) {
        return this.activeTab === tab;
    },

    setActive(tab) {
        this.activeTab = tab;
    }
}));

Alpine.data('search', () => ({
    query: '',
    results: [],
    loading: false,
    showResults: false,

    async search() {
        if (this.query.length < 2) {
            this.results = [];
            return;
        }

        this.loading = true;
        try {
            const response = await fetch(`/api/v1/products/search?q=${encodeURIComponent(this.query)}`);
            this.results = await response.json();
            this.showResults = true;
        } catch (error) {
            console.error('Search error:', error);
        }
        this.loading = false;
    },

    clear() {
        this.query = '';
        this.results = [];
        this.showResults = false;
    }
}));

// Initialize Alpine
Alpine.start();

// Helper functions
window.formatPrice = (price) => {
    return 'S/ ' + parseFloat(price).toFixed(2);
};

window.copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast
        const event = new CustomEvent('toast', { detail: { message: 'Copiado al portapapeles', type: 'success' } });
        window.dispatchEvent(event);
    });
};
