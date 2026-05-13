<script setup>
import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();

const flash = computed(() => page.props.flash || {});

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    customClass: { popup: 'shadow-sm border' },
});

watch(
    () => flash.value,
    (val) => {
        if (!val) return;
        if (val.success) toast.fire({ icon: 'success', title: val.success });
        if (val.error) toast.fire({ icon: 'error', title: val.error });
        if (val.warning) toast.fire({ icon: 'warning', title: val.warning });
        if (val.info) toast.fire({ icon: 'info', title: val.info });
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <!-- Flash messages render as SweetAlert2 toasts via the watcher above. -->
</template>
