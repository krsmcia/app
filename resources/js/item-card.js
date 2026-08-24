window.itemCard = function (item) {
    return {
        quantity: 1,
        wishlisted: WishlistStore.has(item.id),

        init() {
            window.addEventListener('wishlist-updated', () => {
                this.wishlisted = WishlistStore.has(item.id);
            });
        },

        increase() {
            if (this.quantity < 999) {
                this.quantity++;
            }
        },

        decrease() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },

        toggleWishlist() {
            WishlistStore.toggle(item.id);
        },

        addToCart() {
            CartStore.add({
                ...item,
                quantity: this.quantity,
            });

            this.quantity = 1;

            window.dispatchEvent(
                new CustomEvent('cart-added', {
                    detail: {
                        item,
                    },
                })
            );
        },
    };
};