export function initSearch() {
    const searchInput = document.getElementById('search');
    let debounceTimer;

    searchInput?.addEventListener('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchForm = document.getElementById('searchForm');
            searchForm?.submit();
        }, 300);
    });
}
