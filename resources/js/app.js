
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if (window.Echo) {
    window.Echo.channel('stock')
        .listen('StockUpdated', (e) => {
            const el = document.querySelector(`[data-stock-id="${e.productId}"]`);
            if (el) {
                el.textContent = e.stokBaru;
                el.classList.add('animate-pulse', 'ring-2', 'ring-indigo-400');
                setTimeout(() => el.classList.remove('animate-pulse', 'ring-2', 'ring-indigo-400'), 1500);

                if (e.stokBaru > 5) {
                    el.className = el.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+-\d+/g, '');
                    el.classList.add('bg-indigo-50', 'text-indigo-700');
                } else {
                    el.className = el.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+-\d+/g, '');
                    el.classList.add('bg-red-50', 'text-red-700');
                }

                el.classList.add('inline-flex', 'items-center', 'px-2.5', 'py-0.5', 'rounded-full', 'text-sm', 'font-semibold', 'transition-all', 'duration-300');
            }
        });
}
