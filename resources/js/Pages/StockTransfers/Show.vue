<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ transfer: { type: Object, required: true } });
const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('stock_transfers') + ' #' + transfer.transfer_no" :page-title="t('stock_transfers') + ' #' + transfer.transfer_no">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">{{ transfer.transfer_no }}</h5>
                <Link :href="route('admin.stock-transfers.index')" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>{{ t('from_branch') }}:</strong> {{ transfer.from_branch?.name || '—' }}</div>
                    <div class="col-md-3"><strong>{{ t('to_branch') }}:</strong> {{ transfer.to_branch?.name || '—' }}</div>
                    <div class="col-md-3"><strong>{{ t('transfer_date') }}:</strong> {{ transfer.transfer_date }}</div>
                    <div class="col-md-3"><strong>{{ t('status') }}:</strong> {{ transfer.status }}</div>
                </div>
                <table class="table table-sm">
                    <thead><tr>
                        <th>{{ t('ingredient') }}</th>
                        <th>{{ t('unit') }}</th>
                        <th class="text-end">{{ t('quantity_requested') }}</th>
                        <th class="text-end">{{ t('quantity_sent') }}</th>
                        <th class="text-end">{{ t('quantity_received') }}</th>
                    </tr></thead>
                    <tbody>
                        <tr v-for="i in transfer.items" :key="i.id">
                            <td>{{ i.ingredient?.name }}</td>
                            <td>{{ i.unit?.name || '—' }}</td>
                            <td class="text-end">{{ Number(i.quantity_requested).toFixed(4) }}</td>
                            <td class="text-end">{{ Number(i.quantity_sent).toFixed(4) }}</td>
                            <td class="text-end">{{ Number(i.quantity_received).toFixed(4) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
