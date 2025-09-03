import './bootstrap';
import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';
import Swal from 'sweetalert2';

// Import fitur-fitur
import { initBulkActions } from './features/bulkActions';
import { initBarcodeScanner } from './features/barcodeScanner';
import { initCategories } from './features/categories/crud';
import { initCategoryFilter } from './features/categories/filter';
import { initSearch } from './features/search';

// Expose libraries yang dibutuhkan ke window object
window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;
window.Swal = Swal;

// Inisialisasi fitur ketika DOM ready
document.addEventListener("DOMContentLoaded", function () {
    initBulkActions();
    initBarcodeScanner();
    initCategories();
    initCategoryFilter();
    initSearch();
});