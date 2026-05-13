<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    order: { type: Object, required: true },
});

const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('orders') + ' #' + order.order_no" :page-title="t('orders') + ' #' + order.order_no">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ t('orders') }} #{{ order.order_no }}</h5>
                <Link :href="route('admin.orders.index')" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <dl class="row mb-3">
                    <dt class="col-sm-3">{{ t('type') }}</dt><dd class="col-sm-9">{{ order.order_type }}</dd>
                    <dt class="col-sm-3">{{ t('status') }}</dt><dd class="col-sm-9">{{ order.status }}</dd>
                    <dt class="col-sm-3">{{ t('payment_status') }}</dt><dd class="col-sm-9">{{ order.payment_status }}</dd>
                    <dt class="col-sm-3">{{ t('grand_total') }}</dt><dd class="col-sm-9">{{ Number(order.grand_total).toFixed(2) }}</dd>
                    <dt class="col-sm-3">{{ t('paid_amount') }}</dt><dd class="col-sm-9">{{ Number(order.paid_amount).toFixed(2) }}</dd>
                </dl>

                <h6>{{ t('items') }}</h6>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ t('item') }}</th>
                            <th class="text-end">{{ t('quantity') }}</th>
                            <th class="text-end">{{ t('unit_price') }}</th>
                            <th class="text-end">{{ t('line_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in order.items" :key="item.id">
                            <td>{{ item.item_name }}</td>
                            <td class="text-end">{{ item.quantity }}</td>
                            <td class="text-end">{{ Number(item.unit_price).toFixed(2) }}</td>
                            <td class="text-end">{{ Number(item.line_total).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
