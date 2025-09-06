import Swal from 'sweetalert2';

export function showNotification(message, type) {
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

export function showLocalCategoryNotification(message, type) {
    let icon, title, confirmButtonColor, background;
    
    if (type === 'success') {
        icon = 'success';
        title = 'Berhasil!';
        confirmButtonColor = '#10B981';
        background = '#F0FDF4';
    } else if (type === 'error') {
        icon = 'error';
        title = 'Gagal!';
        confirmButtonColor = '#EF4444';
        background = '#FEF2F2';
    }

    const categoryModal = document.getElementById('categoryCrudModal');
    let target = categoryModal;

    if (categoryModal && categoryModal.classList.contains('hidden')) {
        target = document.body;
    }

    Swal.fire({
        icon: icon,
        title: title,
        html: message,
        confirmButtonColor: confirmButtonColor,
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
