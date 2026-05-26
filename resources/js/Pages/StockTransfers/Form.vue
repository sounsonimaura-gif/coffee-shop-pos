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
    from_branch_id: m.from_branch_id ?? null,
    to_branch_id: m.to_branch_id ?? null,
    from_warehouse_id: m.from_warehouse_id ?? null,
    to_warehouse_id: m.to_warehouse_id ?? null,
    transfer_date: m.transfer_date ?? new Date().toISOString().slice(0, 10),
    status: m.status ?? 'received',
    note: m.note ?? '',
    items: m.items?.length
        ? m.items.map((i) => ({
              ingredient_id: i.ingredient_id,
              unit_id: i.unit_id ?? null,
              quantity: Number(i.quantity ?? i.quantity_requested ?? 0),
          }))
        : [{ ingredient_id: null, unit_id: null, quantity: 1 }],
});

function addRow() {
    form.items.push({ ingredient_id: null, unit_id: null, quantity: 1 });
}
function removeRow(i) {
    if (form.items.length > 1) form.items.splice(i, 1);
}
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('stock_transfers')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('stock_transfers')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('stock_transfers') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <FormField :label="t('from_branch')" :error="form.errors.from_branch_id">
                            <TomSelectField v-model="form.from_branch_id" :options="props.props.branches || []" value-field="id" label-field="name" clearable />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('to_branch')" :error="form.errors.to_branch_id">
                            <TomSelectField v-model="form.to_branch_id" :options="props.props.branches || []" value-field="id" label-field="name" clearable />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('transfer_date')" :error="form.errors.transfer_date">
                            <FlatpickrField v-model="form.transfer_date" />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('status')" :error="form.errors.status">
                            <TomSelectField v-model="form.status" :options="props.props.transfer_statuses || []" value-field="value" label-field="text" />
                        </FormField>
                    </div>
                    <div class="col-md-12">
                        <FormField :label="t('note')">
                            <input v-model="form.note" type="text" class="form-control" />
                        </FormField>
                    </div>
                </div>

                <hr />
                <h6 class="mb-3">{{ t('items') }}</h6>
                <table class="table table-sm align-middle">
                    <thead><tr>
                        <th style="min-width: 240px;">{{ t('ingredient') }}</th>
                        <th style="min-width: 140px;">{{ t('unit') }}</th>
                        <th class="text-end" style="width: 160px;">{{ t('quantity') }}</th>
                        <th style="width: 50px;"></th>
                    </tr></thead>
                    <tbody>
                        <tr v-for="(row, i) in form.items" :key="i">
                            <td>
                                <TomSelectField v-model="row.ingredient_id" :options="props.props.ingredients || []" value-field="id" label-field="name" />
                            </td>
                            <td>
                                <TomSelectField v-model="row.unit_id" :options="props.props.units || []" value-field="id" label-field="name" clearable />
                            </td>
                            <td><input v-model.number="row.quantity" type="number" min="0" step="0.0001" class="form-control form-control-sm text-end" /></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" :disabled="form.items.length === 1" @click="removeRow(i)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" @click="addRow">
                    <i class="bi bi-plus me-1"></i>{{ t('add_line') }}
                </button>
                <div v-if="form.errors.items" class="text-danger mt-2 small">{{ form.errors.items }}</div>
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
