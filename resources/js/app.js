import './bootstrap';
import './cart-store';
import './item-card';
import './wishlist-store';
document.addEventListener('wheel', function (event) {
    if (event.target.matches('input[type="number"]')) {
        event.preventDefault();
    }
}, { passive: false });