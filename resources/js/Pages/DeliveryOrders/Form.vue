<script setup>
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
    order_id: m.order_id ?? null,
    delivery_staff_id: m.delivery_staff_id ?? null,
    delivery_address: m.delivery_address ?? '',
    receiver_name: m.receiver_name ?? '',
    receiver_phone: m.receiver_phone ?? '',
    delivery_fee: m.delivery_fee ?? 0,
    distance_km: m.distance_km ?? 0,
    status: m.status ?? 'pending',
    note: m.note ?? '',
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('delivery_orders')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('delivery_orders')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('delivery_orders') }}</h5>
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
                    <FormField :label="t('order')" :error="form.errors.order_id">
                        <TomSelectField v-model="form.order_id" :options="props.props.orders || []" value-field="id" label-field="order_no" clearable />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('delivery_staff')" :error="form.errors.delivery_staff_id">
                        <TomSelectField v-model="form.delivery_staff_id" :options="props.props.staff || []" value-field="id" label-field="name" clearable />
                    </FormField>
                </div>
                <div class="col-md-12">
                    <FormField :label="t('delivery_address')" :error="form.errors.delivery_address" required>
                        <input v-model="form.delivery_address" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('receiver_name')" :error="form.errors.receiver_name">
                        <input v-model="form.receiver_name" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('receiver_phone')" :error="form.errors.receiver_phone">
                        <input v-model="form.receiver_phone" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-2">
                    <FormField :label="t('delivery_fee')" :error="form.errors.delivery_fee">
                        <input v-model.number="form.delivery_fee" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-2">
                    <FormField :label="t('distance_km')" :error="form.errors.distance_km">
                        <input v-model.number="form.distance_km" type="number" step="0.01" class="form-control text-end" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('status')" :error="form.errors.status">
                        <TomSelectField v-model="form.status" :options="props.props.statuses || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
                <div class="col-md-12">
                    <FormField :label="t('note')" :error="form.errors.note">
                        <textarea v-model="form.note" class="form-control" rows="2" />
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
