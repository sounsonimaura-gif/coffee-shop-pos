import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';

/**
 * Show a SweetAlert2 confirm-delete dialog. If the user confirms, fire
 * `router.delete(url)`. Translation strings should be passed in by the
 * caller so they reflect the current locale.
 */
export function confirmDelete({
    url,
    title,
    text,
    confirmButtonText,
    cancelButtonText,
    successMessage,
    onSuccess,
    onError,
} = {}) {
    return Swal.fire({
        title: title || 'Are you sure?',
        text: text || 'You will not be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmButtonText || 'Yes, delete it!',
        cancelButtonText: cancelButtonText || 'Cancel',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) return Promise.resolve(false);

        return new Promise((resolve) => {
            router.delete(url, {
                preserveScroll: true,
                preserveState: true,
                onSuccess: (page) => {
                    if (typeof onSuccess === 'function') onSuccess(page);
                    if (successMessage) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: successMessage,
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true,
                        });
                    }
                    resolve(true);
                },
                onError: (err) => {
                    if (typeof onError === 'function') onError(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            (err && (err.message || Object.values(err)[0])) ||
                            'Something went wrong.',
                    });
                    resolve(false);
                },
            });
        });
    });
}

export default confirmDelete;
