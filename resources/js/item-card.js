window.itemCard = function (item) {
    return {
        quantity: 1,

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

        addToCart() {
            CartStore.add({
                ...item,
                quantity: this.quantity,
            });

            // 장바구니 추가 후 다시 1개로 초기화
            this.quantity = 1;

            // 필요하면 Toast용 이벤트
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