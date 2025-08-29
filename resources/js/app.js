import './bootstrap';
import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;
window.Swal = Swal;

document.addEventListener("DOMContentLoaded", function () {
    // ======================================
    // DOM ELEMENTS
    // ======================================
    // Checkbox & Bulk Actions
    const tambahBarangBtn = document.getElementById('tambahBarangBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.itemCheckbox');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    // Modals
    const confirmModal = document.getElementById('confirmDeleteModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');

    const categoryCrudModal = document.getElementById('categoryCrudModal');
    const openCrudModalBtn = document.getElementById('openCrudModalBtn');
    const closeCrudModalBtn = document.getElementById('closeCategoryCrudModalBtn');
    const categorySearchInput = document.getElementById('categorySearch');
    const categoryList = document.getElementById('categoryList');
    const categoryNotFoundMsg = document.getElementById('categoryNotFoundMsg');

    const editCategoryModal = document.getElementById('editCategoryModal');
    const editCategoryForm = document.getElementById('editCategoryForm');
    const editCategoryNameInput = document.getElementById('editCategoryName');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    const confirmDeleteCategoryModal = document.getElementById('confirmDeleteCategoryModal');
    const deleteCategoryMoveBtn = document.getElementById('deleteCategoryMoveBtn');
    const deleteCategoryDeleteBtn = document.getElementById('deleteCategoryDeleteBtn');
    const cancelCategoryDeleteBtn = document.getElementById('cancelCategoryDeleteBtn');

    const barcodeModal = document.getElementById('barcodeModal');
    const openScannerBtn = document.getElementById('openScannerBtn');
    const closeScannerBtn = document.getElementById('closeScannerBtn');
    const searchInput = document.getElementById('search');

    // Filter Kategori
    const openCategoryFilter = document.getElementById('openCategoryFilter');
    const chipPickerPanel = document.getElementById('chipPickerPanel');
    const chipPickerList = document.getElementById('chipPickerList');
    const applyCategoryFilter = document.getElementById('applyCategoryFilter');
    const closeChipPicker = document.getElementById('closeChipPicker');
    const searchForm = document.getElementById('searchForm');
    const selectedChipsDiv = document.getElementById('selectedChips');
    const selectedChipsContainer = document.getElementById('selectedChipsContainer');
    const categoriesHiddenInputsContainer = document.getElementById('categoriesHiddenInputs');

    // Notifikasi
    const notificationContainer = document.getElementById('notification-container');
    const modalNotificationContainer = document.getElementById('modalNotificationContainer');

    // ======================================
    // STATE & HELPERS
    // ======================================
    let pendingDeleteData = { type: null, form: null };
    let pendingCategoryDeleteData = null; // State untuk hapus kategori
    let html5QrCode = null;
    let debounceTimer;

    // Helper untuk menambahkan kategori dengan urutan alfabetis
    function insertCategoryInOrder(newCategoryElement, newCategoryName) {
        if (!categoryList) return;
        
        const existingCategories = Array.from(categoryList.querySelectorAll('.category-item'));
        const notFoundMsg = document.getElementById('categoryNotFoundMsg');
        
        // Cari posisi yang tepat untuk kategori baru
        let insertPosition = null;
        
        for (let i = 0; i < existingCategories.length; i++) {
            const currentCategory = existingCategories[i];
            
            // Skip pesan "tidak ditemukan"
            if (currentCategory.id === 'categoryNotFoundMsg') continue;
            
            const currentName = currentCategory.dataset.name;
            
            // Jika kategori baru harus ditempatkan sebelum kategori saat ini
            if (newCategoryName.toLowerCase() < currentName.toLowerCase()) {
                insertPosition = currentCategory;
                break;
            }
        }
        
        // Insert kategori baru di posisi yang tepat
        if (insertPosition) {
            categoryList.insertBefore(newCategoryElement, insertPosition);
        } else {
            // Jika tidak ada posisi sebelumnya, tambahkan sebelum pesan "tidak ditemukan"
            if (notFoundMsg) {
                categoryList.insertBefore(newCategoryElement, notFoundMsg);
            } else {
                categoryList.appendChild(newCategoryElement);
            }
        }
    }

    // Helper untuk menampilkan notifikasi di halaman menggunakan Sweet Alert
    function showNotification(message, type) {
        let icon, title, confirmButtonColor;
        
        if (type === 'success') {
            icon = 'success';
            title = 'Berhasil!';
            confirmButtonColor = '#10B981'; // green-500
        } else if (type === 'error') {
            icon = 'error';
            title = 'Oops...';
            confirmButtonColor = '#EF4444'; // red-500
        } else {
            icon = 'warning';
            title = 'Perhatian!';
            confirmButtonColor = '#F59E0B'; // yellow-500
        }

        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            confirmButtonColor: confirmButtonColor,
            confirmButtonText: 'OK',
            timer: 4000,
            timerProgressBar: true,
            position: 'top-end',
            toast: true,
            showConfirmButton: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    // #1: Helper baru untuk menampilkan notifikasi lokal di bawah form tambah kategori menggunakan Sweet Alert
    function showLocalCategoryNotification(message, type) {
        let icon, title, confirmButtonColor, background;
        
        if (type === 'success') {
            icon = 'success';
            title = 'Berhasil!';
            confirmButtonColor = '#10B981'; // green-500
            background = '#F0FDF4'; // green-50
        } else if (type === 'error') {
            icon = 'error';
            title = 'Gagal!';
            confirmButtonColor = '#EF4444'; // red-500
            background = '#FEF2F2'; // red-50
        }

        // Cari modal kategori untuk menentukan target
        const categoryModal = document.getElementById('categoryCrudModal');
        let target = categoryModal;

        // Jika modal tidak terlihat, gunakan target utama
        if (categoryModal && categoryModal.classList.contains('hidden')) {
            target = document.body;
        }

        Swal.fire({
            icon: icon,
            title: title,
            html: message, // Menggunakan html instead of text untuk support bold tags
            confirmButtonColor: confirmButtonColor,
            confirmButtonText: 'OK',
            timer: 3500,
            timerProgressBar: true,
            position: 'center',
            backdrop: false,
            allowOutsideClick: false,
            customClass: {
                container: 'swal-category-container',
                popup: 'swal-category-popup'
            },
            target: target,
            didOpen: (popup) => {
                popup.style.backgroundColor = background;
                popup.style.border = type === 'success' ? '2px solid #10B981' : '2px solid #EF4444';
                popup.addEventListener('mouseenter', Swal.stopTimer);
                popup.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    // Helper untuk menampilkan modal konfirmasi menggunakan Sweet Alert
    function showConfirmModal(title, body, actions) {
        // Untuk kompatibilitas dengan kode existing, kita convert ke Sweet Alert
        Swal.fire({
            icon: 'warning',
            title: title,
            html: body,
            showCancelButton: true,
            confirmButtonColor: '#EF4444', // red-600
            cancelButtonColor: '#6B7280', // gray-500
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'px-6 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors',
                cancelButton: 'px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Cari action dengan id confirmDeleteBtn dan jalankan handlernya
                const confirmAction = actions.find(action => action.id === 'confirmDeleteBtn');
                if (confirmAction && confirmAction.handler) {
                    confirmAction.handler();
                }
            }
            // Jika dibatalkan, Sweet Alert otomatis menutup
        });
    }

    // Helper untuk mengelola tombol bulk action
    function toggleActionButtons() {
        const checkedCount = document.querySelectorAll('.itemCheckbox:checked').length;
        if (checkedCount > 0) {
            deleteSelectedBtn?.classList.remove('hidden');
            tambahBarangBtn?.classList.add('opacity-50', 'pointer-events-none');
        } else {
            deleteSelectedBtn?.classList.add('hidden');
            tambahBarangBtn?.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    // Helper untuk mengelola state filter kategori
    let selectedIds = new Set();
    if (categoriesHiddenInputsContainer) {
        Array.from(categoriesHiddenInputsContainer.querySelectorAll('input[name="categories[]"]'))
            .forEach(input => selectedIds.add(parseInt(input.value, 10)));
    }
    let tempSelectedIds = new Set([...selectedIds]);

    function updateSelectedChipsDisplay() {
        if (!selectedChipsDiv) return;
        selectedChipsDiv.innerHTML = '';
        if (selectedIds.size === 0) {
            selectedChipsContainer?.classList.add('hidden');
            return;
        }
        selectedChipsContainer?.classList.remove('hidden');
        selectedIds.forEach(id => {
            const btn = document.querySelector(`.chipPickerItem[data-id="${id}"]`);
            const name = btn ? btn.dataset.name : `ID: ${id}`;
            const chip = document.createElement('span');
            chip.className = 'selectedChip px-3 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-sm flex items-center gap-2';
            chip.dataset.id = id;
            chip.innerHTML = `<span>${name}</span>
                                <button type="button" class="removeChip -mr-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>`;
            selectedChipsDiv.appendChild(chip);
        });
    }

    function syncPopoverStates() {
        if (!chipPickerList) return;
        chipPickerList.querySelectorAll('.chipPickerItem').forEach(item => {
            const id = parseInt(item.dataset.id, 10);
            if (tempSelectedIds.has(id)) {
                item.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                item.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-200', 'dark:border-gray-700');
            } else {
                item.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                item.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-200', 'dark:border-gray-700');
            }
        });
    }

    // ======================================
    // EVENT LISTENERS
    // ======================================
    // Checkbox & Bulk Actions
    document.querySelectorAll('.itemCheckbox').forEach(cb => {
        cb.addEventListener('change', toggleActionButtons);
    });

    selectAll?.addEventListener('change', function () {
        itemCheckboxes.forEach(cb => { cb.checked = this.checked; });
        toggleActionButtons();
    });

    deleteSelectedBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        const selectedIds = Array.from(itemCheckboxes).filter(cb => cb.checked).map(cb => cb.value);

        if (selectedIds.length === 0) {
            showNotification('Tidak ada item yang dipilih.', 'error');
            return;
        }

        pendingDeleteData.type = 'bulk';
        pendingDeleteData.ids = selectedIds;
        pendingDeleteData.form = bulkDeleteForm;

        const title = 'Hapus Item Terpilih?';
        const body = `Anda akan menghapus <span class="font-semibold">${selectedIds.length}</span> item. Tindakan ini tidak bisa dibatalkan.`;
        showConfirmModal(title, body, [
            { id: 'cancelDeleteBtn', text: 'Batal', className: 'px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors', handler: () => {} },
            { id: 'confirmDeleteBtn', text: 'Ya, Hapus', className: 'px-6 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors', handler: () => {
                const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');
                bulkDeleteInputs.innerHTML = '';
                pendingDeleteData.ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkDeleteInputs.appendChild(input);
                });
                pendingDeleteData.form.submit();
            }}
        ]);
    });

    document.querySelectorAll('.delete-item-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            const itemName = this.dataset.itemName;
            const deleteForm = document.getElementById(`delete-form-${itemId}`);

            pendingDeleteData.type = 'individual_item';
            pendingDeleteData.form = deleteForm;
            const title = 'Hapus Barang ini?';
            const body = `Apakah Anda yakin ingin menghapus barang <span class="font-semibold">${itemName}</span>? Tindakan ini tidak bisa dibatalkan.`;
            showConfirmModal(title, body, [
                { id: 'cancelDeleteBtn', text: 'Batal', className: 'px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors', handler: () => {} },
                { id: 'confirmDeleteBtn', text: 'Ya, Hapus', className: 'px-6 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors', handler: () => {
                    pendingDeleteData.form.submit();
                }}
            ]);
        });
    });

    // Barcode Scanner
    function startScanner() {
        if (html5QrCode) return;
        try { html5QrCode = new Html5Qrcode("reader"); }
        catch (err) { console.error('Html5Qrcode init failed', err); return; }

        Html5Qrcode.getCameras()
            .then(devices => {
                const config = { fps: 10, qrbox: 250 };
                const camera = devices.length ? { facingMode: "environment" } : { facingMode: "user" };
                html5QrCode.start(camera, config, onScanSuccess, onScanError)
                    .catch(err => console.error('QR start failed:', err));
            })
            .catch(err => console.error('Camera error:', err));
    }

    function onScanSuccess(codeText) {
        if (searchInput) searchInput.value = codeText;
        safeStopScanner(true);
        document.getElementById('searchForm')?.submit();
    }
    function onScanError(_) {}

    function safeStopScanner(closeModal = false) {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear(); html5QrCode = null;
                if (closeModal) barcodeModal?.classList.add('hidden');
            }).catch(() => {
                html5QrCode = null;
                if (closeModal) barcodeModal?.classList.add('hidden');
            });
        } else if (closeModal) {
            barcodeModal?.classList.add('hidden');
        }
    }

    openScannerBtn?.addEventListener('click', () => {
        barcodeModal?.classList.remove('hidden');
        startScanner();
    });

    closeScannerBtn?.addEventListener('click', () => safeStopScanner(true));

    barcodeModal?.addEventListener('click', (e) => {
        if (e.target === barcodeModal) safeStopScanner(true);
    });

    // Modal Kelola Kategori (CRUD)
    openCrudModalBtn?.addEventListener('click', () => {
        categoryCrudModal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });

    closeCrudModalBtn?.addEventListener('click', () => {
        categoryCrudModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });

    // Hapus kategori dengan validasi (menggunakan AJAX) - Event Delegation
    categoryList?.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-category-btn');
        if (!deleteBtn) return;
        
        e.preventDefault();
        const catId = deleteBtn.dataset.id;
        const catName = deleteBtn.dataset.name;
        const deleteForm = document.getElementById(`delete-category-form-${catId}`);

        if (!deleteForm) {
            showLocalCategoryNotification('Formulir penghapusan tidak ditemukan.', 'error');
            return;
        }
        
        pendingCategoryDeleteData = { id: catId, name: catName };

            fetch(`/categories/${catId}/item-count`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mendapatkan informasi kategori.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.is_default) {
                        showLocalCategoryNotification('Kategori default tidak dapat dihapus.', 'error');
                        return;
                    }

                    const itemCount = data.item_count;
                    if (itemCount > 0) {
                        document.getElementById('itemCountSpan').textContent = itemCount;
                        confirmDeleteCategoryModal?.classList.remove('hidden');
                    } else {
                    // Kategori kosong, konfirmasi langsung dengan Sweet Alert
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus Kategori?',
                        text: 'Tindakan ini tidak dapat dikembalikan.',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            handleDeleteCategory('delete_only');
                        }
                    });
                }

                })
                .catch(error => {
                    showLocalCategoryNotification(error.message, 'error');
                });
    });

    deleteCategoryMoveBtn?.addEventListener('click', () => handleDeleteCategory('move_items'));
    deleteCategoryDeleteBtn?.addEventListener('click', () => handleDeleteCategory('delete_items'));
    cancelCategoryDeleteBtn?.addEventListener('click', () => confirmDeleteCategoryModal?.classList.add('hidden'));

    async function handleDeleteCategory(action) {
        if (!pendingCategoryDeleteData) return;
        const catId = pendingCategoryDeleteData.id;
        
        const form = document.getElementById(`delete-category-form-${catId}`);
        const actionUrl = form.action;
        
        const formData = new FormData();
        formData.append('_token', form.querySelector('input[name="_token"]').value);
        formData.append('_method', 'DELETE');
        formData.append('action', action);

        confirmDeleteCategoryModal?.classList.add('hidden');
        
        try {
            const response = await fetch(actionUrl, {
                method: 'POST', // Menggunakan POST karena method spoofing
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            const data = await response.json();

            // #2: Menggunakan notifikasi lokal untuk kategori
            if (data.success) {
            showLocalCategoryNotification(data.message, 'success');
            const categoryItem = document.querySelector(`.category-item button[data-id="${catId}"]`);
            if (categoryItem) {
                const li = categoryItem.closest('li');
                li.remove();
            }
            } else {
                showLocalCategoryNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error saat menghapus kategori:', error);
            showLocalCategoryNotification('Terjadi kesalahan saat menghapus kategori. Silakan coba lagi.', 'error');
        }
    }

    // Edit kategori (menggunakan AJAX) - Event Delegation
    categoryList?.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-category-btn');
        if (!editBtn) return;
        
        const catId = editBtn.dataset.id;
        const catName = editBtn.dataset.name;

        // #3: Frontend check untuk mencegah edit kategori default
        if (catName === 'Belum Dikategorikan') {
            showLocalCategoryNotification('Kategori default tidak dapat diedit.', 'error');
            return;
        }

        const updateUrl = `/categories/${catId}`;
        editCategoryForm.setAttribute('action', updateUrl);
        editCategoryNameInput.value = catName;
        editCategoryModal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });

    // #4: Hanya menggunakan tombol batal
    cancelEditBtn?.addEventListener('click', () => {
        editCategoryModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });

    // Submit form edit kategori dengan AJAX
    editCategoryForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const actionUrl = form.action;
        const formData = new FormData(form);

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            const data = await response.json();
            
            // #2: Menggunakan notifikasi lokal untuk edit kategori
            if (response.ok && data.success) {
                showLocalCategoryNotification(data.message, 'success'); // notifikasi lokal kategori
                editCategoryModal?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                // Update kategori yang sudah ada dengan mempertahankan urutan
                const categoryItem = document.querySelector(`.category-item button[data-id="${data.id}"]`);
                if (categoryItem) {
                    const oldCategoryElement = categoryItem.closest('li');
                    const oldName = oldCategoryElement.dataset.name;
                    
                    // Update data attributes dan teks
                    categoryItem.dataset.name = data.name;
                    oldCategoryElement.dataset.name = data.name;
                    oldCategoryElement.querySelector('span').textContent = data.name;
                    
                    // Jika nama berubah, pindahkan ke posisi yang benar secara alfabetis
                    if (oldName.toLowerCase() !== data.name.toLowerCase()) {
                        // Hapus dari posisi lama
                        oldCategoryElement.remove();
                        
                        // Tambahkan kembali dengan urutan yang benar
                        insertCategoryInOrder(oldCategoryElement, data.name);
                    }
                }
            } else {
                showLocalCategoryNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error saat mengupdate kategori:', error);
            showLocalCategoryNotification('Terjadi kesalahan saat mengupdate kategori. Silakan coba lagi.', 'error');
        }
    });

    // Fitur Pencarian Kategori di dalam modal
    categorySearchInput?.addEventListener('keyup', function () {
        const searchText = this.value.toLowerCase();
        const categories = categoryList.querySelectorAll('.category-item');
        let found = false;

        categories.forEach(item => {
            const categoryName = item.dataset.name.toLowerCase();
            if (categoryName.includes(searchText)) {
                item.classList.remove('hidden');
                found = true;
            } else {
                item.classList.add('hidden');
            }
        });

        if (categoryNotFoundMsg) {
            if (found) {
                categoryNotFoundMsg.classList.add('hidden');
            } else {
                categoryNotFoundMsg.classList.remove('hidden');
            }
        }
    });
    
    // Fitur Tambah Kategori (menggunakan AJAX)
    const addCategoryForm = document.getElementById('addCategoryForm');
    addCategoryForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const actionUrl = form.action;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            const data = await response.json();
            
            if (response.ok && data.success) {
                if (response.ok && data.success) {
                showLocalCategoryNotification(`${data.message}`, 'success'); // tampilkan notifikasi lokal kategori
                form.reset();

                // Tambahkan kategori baru ke list DOM tanpa reload
                const newCategory = document.createElement('li');
                newCategory.className = "category-item flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2";
                newCategory.dataset.name = data.name;
                newCategory.innerHTML = `
                    <span class="font-medium text-gray-900 dark:text-gray-100">${data.name}</span>
                    <div class="flex items-center gap-2">
                        <button type="button" class="p-1 rounded-full text-yellow-500 hover:text-yellow-600 edit-category-btn"
                                data-id="${data.id}" data-name="${data.name}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-400 hover:text-yellow-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <form id="delete-category-form-${data.id}" action="/categories/${data.id}" method="POST" class="inline-block">
                            <input type="hidden" name="_token" value="${form.querySelector('input[name="_token"]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="p-1 rounded-full text-red-500 hover:text-red-600 delete-category-btn"
                                    data-id="${data.id}" data-name="${data.name}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-400 hover:text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                `;
                
                // Tambahkan kategori baru dengan urutan alfabetis
                insertCategoryInOrder(newCategory, data.name);
            } else {
                showLocalCategoryNotification(`Gagal menambah kategori: ${data.message}`, 'error');
            }
            } else {
                showLocalCategoryNotification(`Gagal menambah kategori: ${data.message}`, 'error');
            }
        } catch (error) {
            console.error('Error saat menambahkan kategori:', error);
            showLocalCategoryNotification('Terjadi kesalahan saat menambahkan kategori. Silakan coba lagi.', 'error');
        }
    });

    // Filter Kategori (Chips)
    updateSelectedChipsDisplay();
    syncPopoverStates();

    openCategoryFilter?.addEventListener('click', (e) => {
        e.stopPropagation();
        chipPickerPanel?.classList.toggle('hidden');
        tempSelectedIds = new Set([...selectedIds]);
        syncPopoverStates();
    });

    chipPickerList?.addEventListener('click', (e) => {
        const item = e.target.closest('.chipPickerItem');
        if (!item) return;
        const id = parseInt(item.dataset.id, 10);
        if (tempSelectedIds.has(id)) tempSelectedIds.delete(id);
        else tempSelectedIds.add(id);
        syncPopoverStates();
    });

    applyCategoryFilter?.addEventListener('click', () => {
        selectedIds = new Set([...tempSelectedIds]);
        if (categoriesHiddenInputsContainer) {
            categoriesHiddenInputsContainer.innerHTML = '';
        }
        selectedIds.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'categories[]';
            hidden.value = id;
            categoriesHiddenInputsContainer?.appendChild(hidden);
        });
        searchForm?.submit();
        chipPickerPanel?.classList.add('hidden');
        updateSelectedChipsDisplay();
    });

    closeChipPicker?.addEventListener('click', () => {
        chipPickerPanel?.classList.add('hidden');
        tempSelectedIds = new Set([...selectedIds]);
        syncPopoverStates();
    });

    selectedChipsDiv?.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.removeChip');
        if (!removeBtn) return;
        const chip = removeBtn.closest('.selectedChip');
        const idToRemove = parseInt(chip.dataset.id, 10);
        selectedIds.delete(idToRemove);
        chip.remove();
        const hiddenInput = categoriesHiddenInputsContainer?.querySelector(`input[name="categories[]"][value="${idToRemove}"]`);
        hiddenInput?.remove();
        searchForm?.submit();
        updateSelectedChipsDisplay();
    });

    document.addEventListener('click', (e) => {
        if (!chipPickerPanel || chipPickerPanel.classList.contains('hidden')) return;
        if (openCategoryFilter?.contains(e.target) || chipPickerPanel?.contains(e.target)) return;
        chipPickerPanel.classList.add('hidden');
        tempSelectedIds = new Set([...selectedIds]);
        syncPopoverStates();
    });

    // Tambahkan event listener untuk pencarian otomatis
    searchInput?.addEventListener('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchForm = document.getElementById('searchForm');
            searchForm?.submit();
        }, 300);
    });

    // Initial check on page load
    toggleActionButtons();
});

Alpine.start();