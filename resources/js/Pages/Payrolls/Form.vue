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
    payment_method_id: m.payment_method_id ?? null,
    period_month: m.period_month ?? new Date().toISOString().slice(0, 7),
    basic_salary: m.basic_salary ?? 0,
    commission_amount: m.commission_amount ?? 0,
    bonus_amount: m.bonus_amount ?? 0,
    deduction_amount: m.deduction_amount ?? 0,
    payment_date: m.payment_date ?? null,
    status: m.status ?? 'draft',
});

const netSalary = computed(() =>
    Number(form.basic_salary || 0) + Number(form.commission_amount || 0) + Number(form.bonus_amount || 0) - Number(form.deduction_amount || 0)
);

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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('payrolls')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('payrolls')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('payrolls') }}</h5>
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
                    <FormField :label="t('period')" :error="form.errors.period_month" required>
                        <input v-model="form.period_month" type="month" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('payment_method')" :error="form.errors.payment_method_id">
                        <TomSelectField v-model="form.payment_method_id" :options="props.props.payment_methods || []" value-field="id" label-field="name" clearable />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('basic_salary')" :error="form.errors.basic_salary">
                        <input v-model.number="form.basic_salary" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('commission')" :error="form.errors.commission_amount">
                        <input v-model.number="form.commission_amount" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('bonus')" :error="form.errors.bonus_amount">
                        <input v-model.number="form.bonus_amount" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('deduction')" :error="form.errors.deduction_amount">
                        <input v-model.number="form.deduction_amount" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('payment_date')" :error="form.errors.payment_date">
                        <FlatpickrField v-model="form.payment_date" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('status')" :error="form.errors.status">
                        <TomSelectField v-model="form.status" :options="props.props.statuses || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('net_salary')">
                        <input :value="netSalary.toFixed(2)" type="text" readonly class="form-control text-end fw-bold" />
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
