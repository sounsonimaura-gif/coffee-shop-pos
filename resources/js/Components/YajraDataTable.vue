<script setup>
import { onMounted, onBeforeUnmount, ref, watch, computed } from 'vue';
import { useI18n } from '@/composables/useI18n.js';

const $ = window.jQuery;

const props = defineProps({
    ajaxUrl: { type: String, required: true },
    columns: { type: Array, required: true }, // [{ data, name, title, orderable?, searchable?, render? }]
    order: { type: Array, default: () => [[0, 'desc']] },
    pageLength: { type: Number, default: 15 },
    searchPlaceholder: { type: String, default: '' },
    rowId: { type: String, default: 'id' },
    refreshKey: { type: [String, Number], default: 0 },
    extraParams: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();

const tableEl = ref(null);
let dt = null;

function build() {
    if (!tableEl.value) return;
    const $el = $(tableEl.value);

    if ($.fn.DataTable.isDataTable($el)) {
        $el.DataTable().destroy();
        $el.empty();
    }

    dt = $el.DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: props.order,
        pageLength: props.pageLength,
        lengthMenu: [10, 15, 25, 50, 100],
        ajax: {
            url: props.ajaxUrl,
            type: 'GET',
            data: (d) => Object.assign(d, props.extraParams),
        },
        columns: props.columns,
        rowId: props.rowId,
        dom:
            "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex align-items-center justify-content-end'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-2 align-items-center'<'col-sm-6'i><'col-sm-6'p>>",
        // Custom Bootstrap 5 fixed pagination
        pagingType: 'full_numbers',
        language: {
            search: '',
            searchPlaceholder: props.searchPlaceholder || t('search') + '...',
            lengthMenu: t('rows_per_page') + ' _MENU_',
            info: t('showing_entries')
                .replace(':from', '_START_')
                .replace(':to', '_END_')
                .replace(':total', '_TOTAL_'),
            infoEmpty: t('showing_empty'),
            zeroRecords: t('no_data'),
            processing: t('loading'),
            paginate: {
                first: '«',
                previous: '‹',
                next: '›',
                last: '»',
            },
        },
    });
}

function reload() {
    if (dt) dt.ajax.reload(null, false);
}

defineExpose({ reload });

onMounted(() => build());

onBeforeUnmount(() => {
    if (dt) dt.destroy();
});

watch(
    () => props.refreshKey,
    () => reload()
);

watch(
    () => locale.value,
    () => build()
);
</script>

<template>
    <div class="dt-wrapper">
        <table
            ref="tableEl"
            class="table table-striped table-bordered table-hover dataTable w-100"
        >
            <thead>
                <tr>
                    <th v-for="(col, i) in columns" :key="i">{{ col.title }}</th>
                </tr>
            </thead>
        </table>
    </div>
</template>

<style>
.dataTables_wrapper .pagination {
    margin-bottom: 0;
}
</style>
