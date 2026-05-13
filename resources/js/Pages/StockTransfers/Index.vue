<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import YajraDataTable from '@/Components/YajraDataTable.vue';
import confirmDelete from '@/composables/confirmDelete.js';

const props = defineProps({
    titleKey: { type: String, required: true },
    routes: { type: Object, required: true },
    columns: { type: Array, required: true },
    permissions: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const refreshKey = ref(0);

function renderColumns() {
    return [
        ...props.columns.map((c) => ({
            data: c.data,
            name: c.name,
            title: c.title,
            orderable: c.orderable,
            searchable: c.searchable,
            render: (d) => (d === null || d === undefined ? '' : String(d)),
        })),
        { data: 'actions', name: 'actions', title: t('actions'), orderable: false, searchable: false },
    ];
}

function onClick(e) {
    const btn = e.target.closest('.js-confirm-delete');
    if (btn) {
        e.preventDefault();
        confirmDelete({
            url: btn.dataset.url,
            onSuccess: () => refreshKey.value++,
        });
    }
}
</script>

<template>
    <AdminLayout :title="t(titleKey.replace('coffee.', ''))" :page-title="t(titleKey.replace('coffee.', ''))">
        <div class="card" @click="onClick">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ t(titleKey.replace('coffee.', '')) }}</h5>
                <Link v-if="routes.create && permissions.create" :href="route(routes.create)" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>{{ t('new') }}
                </Link>
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
