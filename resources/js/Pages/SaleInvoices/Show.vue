<script setup>
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    invoice: { type: Object, required: true },
});

const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('sale_invoices') + ' #' + invoice.sale_no" :page-title="t('sale_invoices') + ' #' + invoice.sale_no">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ t('sale_invoices') }} #{{ invoice.sale_no }}</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">{{ t('from') }}</dt><dd class="col-sm-9">{{ invoice.sale_at }}</dd>
                    <dt class="col-sm-3">{{ t('grand_total') }}</dt><dd class="col-sm-9">{{ Number(invoice.grand_total).toFixed(2) }}</dd>
                    <dt class="col-sm-3">{{ t('paid_amount') }}</dt><dd class="col-sm-9">{{ Number(invoice.paid_amount).toFixed(2) }}</dd>
                    <dt class="col-sm-3">{{ t('change_amount') }}</dt><dd class="col-sm-9">{{ Number(invoice.change_amount).toFixed(2) }}</dd>
                    <dt class="col-sm-3">{{ t('status') }}</dt><dd class="col-sm-9">{{ invoice.status }}</dd>
                </dl>
            </div>
        </div>
    </AdminLayout>
</template>
