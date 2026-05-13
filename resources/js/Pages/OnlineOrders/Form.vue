<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';
import TomSelectField from '@/Components/TomSelectField.vue';

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
    branch_id: m.branch_id ?? null,
    customer_id: m.customer_id ?? null,
    customer_name: m.customer_name ?? '',
    customer_phone: m.customer_phone ?? '',
    delivery_address: m.delivery_address ?? '',
    subtotal: m.subtotal ?? 0,
    delivery_fee: m.delivery_fee ?? 0,
    payment_method: m.payment_method ?? 'cash',
    status: m.status ?? 'pending',
});

const grandTotal = computed(() => Number(form.subtotal || 0) + Number(form.delivery_fee || 0));

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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('online_orders')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('online_orders')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('online_orders') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <FormField :label="t('branch')" :error="form.errors.branch_id">
                        <TomSelectField v-model="form.branch_id" :options="props.props.branches || []" value-field="id" label-field="name" clearable />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('customer')" :error="form.errors.customer_id">
                        <TomSelectField v-model="form.customer_id" :options="props.props.customers || []" value-field="id" label-field="name" clearable />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('phone')" :error="form.errors.customer_phone">
                        <input v-model="form.customer_phone" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-6">
                    <FormField :label="t('customer_name')" :error="form.errors.customer_name">
                        <input v-model="form.customer_name" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-6">
                    <FormField :label="t('delivery_address')" :error="form.errors.delivery_address">
                        <input v-model="form.delivery_address" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('subtotal')" :error="form.errors.subtotal">
                        <input v-model.number="form.subtotal" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('delivery_fee')" :error="form.errors.delivery_fee">
                        <input v-model.number="form.delivery_fee" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('grand_total')">
                        <input :value="grandTotal.toFixed(2)" type="text" readonly class="form-control text-end fw-bold" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('payment_method')" :error="form.errors.payment_method">
                        <TomSelectField v-model="form.payment_method" :options="props.props.payment_methods || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
                <div class="col-md-4">
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
