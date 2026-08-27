window.CartStore = {
    storageKey: 'cart',

    get() {
        try {
            return JSON.parse(
                localStorage.getItem(this.storageKey) || '{}'
            );
        } catch (error) {
            return {};
        }
    },

    save(cart) {
        localStorage.setItem(
            this.storageKey,
            JSON.stringify(cart)
        );

        window.dispatchEvent(
            new CustomEvent('cart-updated', {
                detail: {
                    cart: cart,
                    count: this.count(),
                },
            })
        );
    },

    add(item) {
        const cart = this.get();

        if (cart[item.id]) {
            cart[item.id].quantity += item.quantity;
        } else {
            cart[item.id] = {
                id: item.id,
                name: item.name,
                sku: item.sku,
                image: item.image,
                quantity: item.quantity,
                unit: item.unit,
                brand: item.brand,
                color: item.color,
                size: item.size,
                remark: '',
            };
        }

        this.save(cart);
    },

    increase(itemId) {
        const cart = this.get();

        if (!cart[itemId]) {
            return;
        }

        cart[itemId].quantity = Math.min(
            cart[itemId].quantity + 1,
            999
        );

        this.save(cart);
    },

    decrease(itemId) {
        const cart = this.get();

        if (!cart[itemId]) {
            return;
        }

        cart[itemId].quantity--;

        if (cart[itemId].quantity < 1) {
            delete cart[itemId];
        }

        this.save(cart);
    },

    updateQuantity(itemId, quantity) {
        const cart = this.get();

        if (!cart[itemId]) {
            return;
        }

        quantity = parseInt(quantity) || 1;

        cart[itemId].quantity = Math.max(
            1,
            Math.min(quantity, 999)
        );

        this.save(cart);
    },

    updateRemark(itemId, remark) {
        const cart = this.get();

        if (!cart[itemId]) {
            return;
        }

        cart[itemId].remark = remark;

        this.save(cart);
    },

    remove(itemId) {
        const cart = this.get();

        delete cart[itemId];

        this.save(cart);
    },

    clear() {
        localStorage.removeItem(this.storageKey);

        window.dispatchEvent(
            new CustomEvent('cart-updated', {
                detail: {
                    cart: {},
                    count: 0,
                },
            })
        );
    },

    items() {
        return Object.values(this.get()).map(item => ({
            ...item,
            remark: item.remark ?? '',
        }));
    },

    count() {
        return this.items().reduce(
            (total, item) => total + item.quantity,
            0
        );
    },

    totalItems() {
        return Object.keys(this.get()).length;
    },
};