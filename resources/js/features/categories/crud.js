import { showLocalCategoryNotification } from '../../utils/notifications';

// Helper untuk menambahkan kategori dengan urutan alfabetis
function insertCategoryInOrder(categoryList, newCategoryElement, newCategoryName) {
    if (!categoryList) return;
    
    const existingCategories = Array.from(categoryList.querySelectorAll('.category-item'));
    const notFoundMsg = document.getElementById('categoryNotFoundMsg');
    
    let insertPosition = null;
    
    for (let i = 0; i < existingCategories.length; i++) {
        const currentCategory = existingCategories[i];
        if (currentCategory.id === 'categoryNotFoundMsg') continue;
        
        const currentName = currentCategory.dataset.name;
        if (newCategoryName.toLowerCase() < currentName.toLowerCase()) {
            insertPosition = currentCategory;
            break;
        }
    }
    
    if (insertPosition) {
        categoryList.insertBefore(newCategoryElement, insertPosition);
    } else {
        if (notFoundMsg) {
            categoryList.insertBefore(newCategoryElement, notFoundMsg);
        } else {
            categoryList.appendChild(newCategoryElement);
        }
    }
}

export function initCategories() {
    const categoryCrudModal = document.getElementById('categoryCrudModal');
    const openCrudModalBtn = document.getElementById('openCrudModalBtn');
    const closeCrudModalBtn = document.getElementById('closeCategoryCrudModalBtn');
    const categoryList = document.getElementById('categoryList');
    const categorySearchInput = document.getElementById('categorySearch');
    const categoryNotFoundMsg = document.getElementById('categoryNotFoundMsg');
    const editCategoryModal = document.getElementById('editCategoryModal');
    const editCategoryForm = document.getElementById('editCategoryForm');
    const editCategoryNameInput = document.getElementById('editCategoryName');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const confirmDeleteCategoryModal = document.getElementById('confirmDeleteCategoryModal');
    const deleteCategoryMoveBtn = document.getElementById('deleteCategoryMoveBtn');
    const deleteCategoryDeleteBtn = document.getElementById('deleteCategoryDeleteBtn');
    const cancelCategoryDeleteBtn = document.getElementById('cancelCategoryDeleteBtn');

    let pendingCategoryDeleteData = null;

    // Modal CRUD
    openCrudModalBtn?.addEventListener('click', () => {
        categoryCrudModal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });

    closeCrudModalBtn?.addEventListener('click', () => {
        categoryCrudModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });

    // Hapus kategori
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
                    handleDeleteCategory('delete_only');
                }
            })
            .catch(error => {
                showLocalCategoryNotification(error.message, 'error');
            });
    });

    // Edit kategori
    categoryList?.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-category-btn');
        if (!editBtn) return;
        
        const catId = editBtn.dataset.id;
        const catName = editBtn.dataset.name;

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

    // Event handlers untuk modal delete
    deleteCategoryMoveBtn?.addEventListener('click', () => handleDeleteCategory('move_items'));
    deleteCategoryDeleteBtn?.addEventListener('click', () => handleDeleteCategory('delete_items'));
    cancelCategoryDeleteBtn?.addEventListener('click', () => confirmDeleteCategoryModal?.classList.add('hidden'));

    // Fungsi untuk menangani penghapusan kategori
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
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            const data = await response.json();

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

    // Form edit kategori
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
            
            if (response.ok && data.success) {
                showLocalCategoryNotification(data.message, 'success');
                editCategoryModal?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                const categoryItem = document.querySelector(`.category-item button[data-id="${data.id}"]`);
                if (categoryItem) {
                    const oldCategoryElement = categoryItem.closest('li');
                    const oldName = oldCategoryElement.dataset.name;
                    
                    categoryItem.dataset.name = data.name;
                    oldCategoryElement.dataset.name = data.name;
                    oldCategoryElement.querySelector('span').textContent = data.name;
                    
                    if (oldName.toLowerCase() !== data.name.toLowerCase()) {
                        oldCategoryElement.remove();
                        insertCategoryInOrder(categoryList, oldCategoryElement, data.name);
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

    // Pencarian kategori
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
            categoryNotFoundMsg.classList.toggle('hidden', found);
        }
    });

    // Form tambah kategori
    const addCategoryForm = document.getElementById('addCategoryForm');
    addCategoryForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            const data = await response.json();
            
            if (response.ok && data.success) {
                showLocalCategoryNotification(data.message, 'success');
                form.reset();

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
                
                insertCategoryInOrder(categoryList, newCategory, data.name);
            } else {
                showLocalCategoryNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error saat menambahkan kategori:', error);
            showLocalCategoryNotification('Terjadi kesalahan saat menambahkan kategori. Silakan coba lagi.', 'error');
        }
    });

    // Modal handling
    cancelEditBtn?.addEventListener('click', () => {
        editCategoryModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });
}
