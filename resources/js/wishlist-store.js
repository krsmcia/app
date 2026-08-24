window.WishlistStore = {

    storageKey: 'wishlist',

    get() {
        try {
            const wishlist = JSON.parse(
                localStorage.getItem(this.storageKey) || '[]'
            );

            return Array.isArray(wishlist)
                ? wishlist.map(Number).filter(id => id > 0)
                : [];
        } catch (error) {
            return [];
        }
    },

    save(wishlist) {
        wishlist = [...new Set(wishlist.map(Number))];

        localStorage.setItem(
            this.storageKey,
            JSON.stringify(wishlist)
        );

        window.dispatchEvent(
            new CustomEvent('wishlist-updated', {
                detail: {
                    count: wishlist.length,
                    wishlist: wishlist,
                }
            })
        );
    },

    has(itemId) {
        return this.get().includes(Number(itemId));
    },

    toggle(itemId) {
        itemId = Number(itemId);

        let wishlist = this.get();

        if (wishlist.includes(itemId)) {
            wishlist = wishlist.filter(id => id !== itemId);
        } else {
            wishlist.push(itemId);
        }

        this.save(wishlist);

        return wishlist;
    },

    count() {
        return this.get().length;
    }
};