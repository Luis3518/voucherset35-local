import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import { setupVouchers } from './vouchers.js';

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

const grid = document.querySelector('#meal-grid');
if (grid) {
    import('./meals.js').then(({ setupMeals }) => setupMeals(grid));
}

const voucherForm = document.querySelector('#voucher-form');
if (voucherForm) {
    setupVouchers(voucherForm);
}
