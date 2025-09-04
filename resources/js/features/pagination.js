/**
 * AJAX Pagination module untuk menangani dropdown items per page tanpa reload
 * Mengintegrasikan dengan filter dan search yang sudah ada
 */

export function initializePagination() {
    const perPageSelect = document.getElementById('perPageSelect');
    
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const perPage = this.value;
            loadItemsData(perPage, 1); // Reset ke halaman 1
        });
    }
    
    // Handle pagination links click
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('a[href*="page="]');
        if (paginationLink && paginationLink.href.includes('/items')) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            const page = url.searchParams.get('page');
            const perPage = url.searchParams.get('per_page') || 25;
            loadItemsData(perPage, page);
        }
    });
}

function loadItemsData(perPage, page = 1) {
    // Ambil parameter URL yang sudah ada
    const urlParams = new URLSearchParams(window.location.search);
    
    // Set parameter baru
    urlParams.set('per_page', perPage);
    urlParams.set('page', page);
    
    // Update URL tanpa reload
    const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    history.pushState(null, '', newUrl);
    
    // Show loading state
    showLoadingState();
    
    // Fetch data baru via AJAX
    fetch(`/items?${urlParams.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Update table content
        updateTableContent(data.html);
        updatePaginationInfo(data.info);
        updatePaginationLinks(data.pagination);
        
        // Update dropdown selection
        updatePerPageSelect(perPage);
        
        // Re-initialize event listeners for new content
        reinitializeEventListeners();
        
        hideLoadingState();
    })
    .catch(error => {
        console.error('Error loading data:', error);
        hideLoadingState();
        
        // Show error message
        if (window.Swal) {
            Swal.fire({
                title: 'Error',
                text: 'Gagal memuat data. Silakan coba lagi.',
                icon: 'error'
            });
        } else {
            alert('Gagal memuat data. Silakan coba lagi.');
        }
    });
}

function updateTableContent(html) {
    const tableBody = document.querySelector('#itemsTable tbody');
    if (tableBody) {
        tableBody.outerHTML = html;
    }
}

function updatePaginationInfo(html) {
    const paginationInfo = document.getElementById('paginationInfo');
    if (paginationInfo) {
        paginationInfo.innerHTML = html;
    }
}

function updatePaginationLinks(html) {
    const paginationLinks = document.getElementById('paginationLinks');
    if (paginationLinks) {
        paginationLinks.innerHTML = html;
    }
}

function updatePerPageSelect(perPage) {
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.value = perPage;
    }
}

function showLoadingState() {
    const table = document.getElementById('itemsTable');
    if (table) {
        table.style.opacity = '0.5';
        table.style.pointerEvents = 'none';
    }
    
    // Add loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loadingOverlay';
    loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
    loadingOverlay.innerHTML = `
        <div class="flex items-center gap-2 text-gray-600">
            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memuat...</span>
        </div>
    `;
    
    const tableContainer = table.closest('.overflow-x-auto');
    if (tableContainer) {
        tableContainer.style.position = 'relative';
        tableContainer.appendChild(loadingOverlay);
    }
}

function hideLoadingState() {
    const table = document.getElementById('itemsTable');
    if (table) {
        table.style.opacity = '1';
        table.style.pointerEvents = 'auto';
    }
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

function reinitializeEventListeners() {
    // Re-initialize bulk actions
    if (window.reinitializeBulkActions) {
        window.reinitializeBulkActions();
    }
    
    // Re-initialize delete buttons untuk single item delete
    initializeDeleteButtons();
    
    // Re-initialize checkboxes
    initializeCheckboxes();
}

function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-item-btn');
    deleteButtons.forEach(button => {
        // Remove any existing listeners to prevent duplicates
        button.removeEventListener('click', handleDeleteClick);
        button.addEventListener('click', handleDeleteClick);
    });
}

function handleDeleteClick(event) {
    const button = event.target;
    const itemId = button.dataset.itemId;
    const itemName = button.dataset.itemName;
    
    if (window.Swal) {
        Swal.fire({
            title: 'Hapus item?',
            text: `Apakah Anda yakin ingin menghapus "${itemName}"? Tindakan ini tidak bisa dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(`delete-form-${itemId}`);
                if (form) {
                    form.submit();
                }
            }
        });
    }
}

function initializeCheckboxes() {
    const checkboxes = document.querySelectorAll('.itemCheckbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    if (selectAllCheckbox) {
        // Remove existing listeners to prevent duplicates
        selectAllCheckbox.removeEventListener('change', handleSelectAllChange);
        selectAllCheckbox.addEventListener('change', handleSelectAllChange);
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.removeEventListener('change', handleCheckboxChange);
        checkbox.addEventListener('change', handleCheckboxChange);
    });
}

function handleSelectAllChange(event) {
    const isChecked = event.target.checked;
    const checkboxes = document.querySelectorAll('.itemCheckbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    // Trigger bulk actions update
    triggerBulkActionsUpdate();
}

function handleCheckboxChange() {
    const checkboxes = document.querySelectorAll('.itemCheckbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    const someChecked = Array.from(checkboxes).some(cb => cb.checked);
    
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }
    
    // Trigger bulk actions update
    triggerBulkActionsUpdate();
}

function triggerBulkActionsUpdate() {
    // Trigger custom event for bulk actions
    const event = new CustomEvent('checkboxStateChanged');
    document.dispatchEvent(event);
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(e) {
    // Reload the page when user navigates back/forward
    window.location.reload();
});

// Auto-initialize ketika DOM loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePagination);
} else {
    initializePagination();
}
