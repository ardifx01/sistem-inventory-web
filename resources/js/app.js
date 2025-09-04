import './bootstrap';
import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';
import Swal from 'sweetalert2';
import { initBarcodeScanner } from './features/barcodeScanner';
import { initBulkActions } from './features/bulkActions';
import { initSearch } from './features/search';
import { initCategories } from './features/categories/crud';
import { initCategoryFilter } from './features/categories/filter';
import { initializePagination } from './features/pagination';

window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;
window.Swal = Swal;

document.addEventListener("DOMContentLoaded", function () {
    // Initialize all modularized features
    initBarcodeScanner();
    initBulkActions();
    initSearch();
    initCategories();
    initCategoryFilter();
    initializePagination();
});

Alpine.start();
Alpine.start();