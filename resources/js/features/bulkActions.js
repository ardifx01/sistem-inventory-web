import { showNotification } from '../utils/notifications';
import { showConfirmModal } from '../utils/modals';

export function initBulkActions() {
    const tambahBarangBtn = document.getElementById('tambahBarangBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.itemCheckbox');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

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

    // Event Listeners
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

        const title = 'Hapus Item Terpilih?';
        const body = `Anda akan menghapus <span class="font-semibold">${selectedIds.length}</span> item. Tindakan ini tidak bisa dibatalkan.`;
        showConfirmModal(title, body, [
            { id: 'confirmDeleteBtn', handler: () => {
                const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');
                bulkDeleteInputs.innerHTML = '';
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkDeleteInputs.appendChild(input);
                });
                bulkDeleteForm.submit();
            }}
        ]);
    });

    // Individual item deletion
    document.querySelectorAll('.delete-item-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            const itemName = this.dataset.itemName;
            const deleteForm = document.getElementById(`delete-form-${itemId}`);

            const title = 'Hapus Barang ini?';
            const body = `Apakah Anda yakin ingin menghapus barang <span class="font-semibold">${itemName}</span>? Tindakan ini tidak bisa dibatalkan.`;
            showConfirmModal(title, body, [
                { id: 'confirmDeleteBtn', handler: () => {
                    deleteForm.submit();
                }}
            ]);
        });
    });

    // Initial check on load
    toggleActionButtons();
    
    // Listen for checkbox state changes from AJAX pagination
    document.addEventListener('checkboxStateChanged', toggleActionButtons);
}

// Export function to re-initialize bulk actions after AJAX updates
export function reinitializeBulkActions() {
    const tambahBarangBtn = document.getElementById('tambahBarangBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    
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
    
    // Re-bind events for new elements
    document.querySelectorAll('.itemCheckbox').forEach(cb => {
        cb.removeEventListener('change', toggleActionButtons);
        cb.addEventListener('change', toggleActionButtons);
    });
    
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.removeEventListener('change', handleSelectAllBulk);
        selectAll.addEventListener('change', handleSelectAllBulk);
    }
    
    function handleSelectAllBulk() {
        const itemCheckboxes = document.querySelectorAll('.itemCheckbox');
        itemCheckboxes.forEach(cb => { cb.checked = selectAll.checked; });
        toggleActionButtons();
    }
    
    // Update button states
    toggleActionButtons();
}

// Make function available globally for pagination module
window.reinitializeBulkActions = reinitializeBulkActions;
