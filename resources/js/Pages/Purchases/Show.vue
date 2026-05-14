<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ purchase: { type: Object, required: true } });
const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('purchases') + ' #' + purchase.purchase_no" :page-title="t('purchases') + ' #' + purchase.purchase_no">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">{{ purchase.purchase_no }}</h5>
                <Link :href="route('admin.purchases.index')" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>{{ t('supplier') }}:</strong> {{ purchase.supplier?.name || '—' }}</div>
                    <div class="col-md-3"><strong>{{ t('warehouse') }}:</strong> {{ purchase.warehouse?.name || '—' }}</div>
                    <div class="col-md-3"><strong>{{ t('purchase_date') }}:</strong> {{ purchase.purchase_date }}</div>
                    <div class="col-md-3"><strong>{{ t('status') }}:</strong> {{ purchase.purchase_status }}</div>
                </div>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>{{ t('ingredient') }}</th>
                            <th>{{ t('batch_no') }}</th>
                            <th class="text-end">{{ t('quantity') }}</th>
                            <th class="text-end">{{ t('unit_cost') }}</th>
                            <th class="text-end">{{ t('line_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="i in purchase.items" :key="i.id">
                            <td>{{ i.ingredient?.name }}</td>
                            <td>{{ i.batch_no || '—' }}</td>
                            <td class="text-end">{{ Number(i.quantity).toFixed(4) }}</td>
                            <td class="text-end">{{ Number(i.unit_cost).toFixed(4) }}</td>
                            <td class="text-end">{{ Number(i.line_total).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="4" class="text-end">{{ t('subtotal') }}</th><th class="text-end">{{ Number(purchase.subtotal).toFixed(2) }}</th></tr>
                        <tr><th colspan="4" class="text-end">{{ t('discount') }}</th><th class="text-end">{{ Number(purchase.discount_amount).toFixed(2) }}</th></tr>
                        <tr><th colspan="4" class="text-end">{{ t('tax') }}</th><th class="text-end">{{ Number(purchase.tax_amount).toFixed(2) }}</th></tr>
                        <tr><th colspan="4" class="text-end">{{ t('shipping') }}</th><th class="text-end">{{ Number(purchase.shipping_amount).toFixed(2) }}</th></tr>
                        <tr class="table-active"><th colspan="4" class="text-end">{{ t('grand_total') }}</th><th class="text-end">{{ Number(purchase.grand_total).toFixed(2) }}</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
