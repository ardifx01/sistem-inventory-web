import Swal from 'sweetalert2';

export function showConfirmModal(title, body, actions) {
    Swal.fire({
        icon: 'warning',
        title: title,
        html: body,
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'px-6 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors',
            cancelButton: 'px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const confirmAction = actions.find(action => action.id === 'confirmDeleteBtn');
            if (confirmAction && confirmAction.handler) {
                confirmAction.handler();
            }
        }
    });
}
