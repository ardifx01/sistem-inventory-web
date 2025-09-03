export function initCategoryFilter() {
    const openCategoryFilter = document.getElementById('openCategoryFilter');
    const chipPickerPanel = document.getElementById('chipPickerPanel');
    const chipPickerList = document.getElementById('chipPickerList');
    const applyCategoryFilter = document.getElementById('applyCategoryFilter');
    const closeChipPicker = document.getElementById('closeChipPicker');
    const searchForm = document.getElementById('searchForm');
    const selectedChipsDiv = document.getElementById('selectedChips');
    const selectedChipsContainer = document.getElementById('selectedChipsContainer');
    const categoriesHiddenInputsContainer = document.getElementById('categoriesHiddenInputs');

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
            chip.innerHTML = `
                <span>${name}</span>
                <button type="button" class="removeChip -mr-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
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

    // Event Listeners
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
}
