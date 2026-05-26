<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';
import TomSelectField from '@/Components/TomSelectField.vue';
import FlatpickrField from '@/Components/FlatpickrField.vue';

const props = defineProps({
    titleKey: { type: String, required: true },
    isEdit: { type: Boolean, default: false },
    model: { type: Object, default: () => ({}) },
    props: { type: Object, default: () => ({}) },
    routes: { type: Object, required: true },
});

const { t } = useI18n();
const m = props.model || {};

const form = useForm({
    staff_id: m.staff_id ?? null,
    branch_id: m.branch_id ?? null,
    sale_invoice_id: m.sale_invoice_id ?? null,
    commission_type: m.commission_type ?? 'sale_percent',
    base_amount: m.base_amount ?? 0,
    rate: m.rate ?? 0,
    commission_date: m.commission_date ?? new Date().toISOString().slice(0, 10),
    status: m.status ?? 'pending',
});

const commissionAmount = computed(() => {
    const t = form.commission_type || 'sale_percent';
    return t.includes('percent')
        ? (Number(form.base_amount || 0) * Number(form.rate || 0)) / 100
        : Number(form.rate || 0);
});

function submit() {
    if (props.isEdit) {
        const [name, id] = props.routes.update;
        form.put(route(name, id));
    } else {
        form.post(route(props.routes.store));
    }
}
</script>

<template>
    <AdminLayout
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('commissions')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('commissions')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('commissions') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <FormField :label="t('staff')" :error="form.errors.staff_id" required>
                        <TomSelectField v-model="form.staff_id" :options="props.props.staff || []" value-field="id" label-field="name" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('sale_invoice')" :error="form.errors.sale_invoice_id">
                        <TomSelectField v-model="form.sale_invoice_id" :options="props.props.sale_invoices || []" value-field="id" label-field="invoice_no" clearable />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('type')" :error="form.errors.commission_type" required>
                        <TomSelectField v-model="form.commission_type" :options="props.props.commission_types || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('base_amount')" :error="form.errors.base_amount" required>
                        <input v-model.number="form.base_amount" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('rate')" :error="form.errors.rate" required>
                        <input v-model.number="form.rate" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('commission_amount')">
                        <input :value="commissionAmount.toFixed(2)" type="text" readonly class="form-control text-end fw-bold" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('date')" :error="form.errors.commission_date" required>
                        <FlatpickrField v-model="form.commission_date" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('status')" :error="form.errors.status">
                        <TomSelectField v-model="form.status" :options="props.props.statuses || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" :disabled="form.processing">
                    <i class="bi bi-check2 me-1"></i>{{ isEdit ? t('update') : t('save') }}
                </button>
                <Link :href="route(routes.index)" class="btn btn-outline-secondary">{{ t('cancel') }}</Link>
            </div>
        </form>
    </AdminLayout>
</template>
