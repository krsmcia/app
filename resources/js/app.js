import './bootstrap';
import './cart-store';
import './item-card';
import './wishlist-store';
import './subscribe';
document.addEventListener('wheel', function (event) {
    if (event.target.matches('input[type="number"]')) {
        event.target.blur();
    }
});