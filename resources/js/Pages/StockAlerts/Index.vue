<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import YajraDataTable from '@/Components/YajraDataTable.vue';

const props = defineProps({
    titleKey: { type: String, required: true },
    routes: { type: Object, required: true },
    columns: { type: Array, required: true },
});

const { t } = useI18n();
const refreshKey = ref(0);

function renderColumns() {
    return props.columns.map((c) => ({
        data: c.data,
        name: c.name,
        title: c.title && c.title.startsWith('coffee.') ? t(c.title.slice(7)) : t(c.title || ''),
        orderable: c.orderable,
        searchable: c.searchable,
        render: (data) => (data === null || data === undefined ? '' : String(data)),
    }));
}

function onClick(e) {
    const btn = e.target.closest('.js-stock-alert-ack');
    if (!btn) return;
    e.preventDefault();
    const url = btn.dataset.url;
    Swal.fire({
        title: t('are_you_sure'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: t('yes'),
        cancelButtonText: t('cancel'),
    }).then((res) => {
        if (!res.isConfirmed) return;
        router.post(url, {}, {
            onSuccess: () => {
                refreshKey.value++;
            },
        });
    });
}
</script>

<template>
    <AdminLayout :title="t(titleKey.replace('coffee.', ''))" :page-title="t(titleKey.replace('coffee.', ''))">
        <div class="card" @click="onClick">
            <div class="card-header">
                <h5 class="mb-0">{{ t(titleKey.replace('coffee.', '')) }}</h5>
            </div>
            <div class="card-body">
                <YajraDataTable
                    :ajax-url="route(routes.data)"
                    :columns="renderColumns()"
                    :refresh-key="refreshKey"
                />
            </div>
        </div>
    </AdminLayout>
</template>
