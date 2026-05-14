<script setup>
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
    warehouse_id: m.warehouse_id ?? null,
    ingredient_id: m.ingredient_id ?? null,
    waste_type: m.waste_type ?? 'waste',
    quantity: m.quantity ?? 1,
    cost_amount: m.cost_amount ?? 0,
    reason: m.reason ?? '',
    waste_date: m.waste_date ?? new Date().toISOString().slice(0, 10),
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('waste_records')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('waste_records')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('waste_records') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <FormField :label="t('ingredient')" :error="form.errors.ingredient_id">
                            <TomSelectField v-model="form.ingredient_id" :options="props.props.ingredients || []" value-field="id" label-field="name" />
                        </FormField>
                    </div>
                    <div class="col-md-6">
                        <FormField :label="t('warehouse')" :error="form.errors.warehouse_id">
                            <TomSelectField v-model="form.warehouse_id" :options="props.props.warehouses || []" value-field="id" label-field="name" clearable />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('type')" :error="form.errors.waste_type">
                            <TomSelectField v-model="form.waste_type" :options="props.props.waste_types || []" value-field="value" label-field="text" />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('quantity')" :error="form.errors.quantity">
                            <input v-model.number="form.quantity" type="number" step="0.0001" min="0" class="form-control" />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('cost')" :error="form.errors.cost_amount">
                            <input v-model.number="form.cost_amount" type="number" step="0.01" min="0" class="form-control" />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('date')" :error="form.errors.waste_date">
                            <FlatpickrField v-model="form.waste_date" />
                        </FormField>
                    </div>
                    <div class="col-md-12">
                        <FormField :label="t('reason')" :error="form.errors.reason">
                            <textarea v-model="form.reason" class="form-control" rows="2" />
                        </FormField>
                    </div>
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
