<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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
    supplier_id: m.supplier_id ?? null,
    warehouse_id: m.warehouse_id ?? null,
    purchase_date: m.purchase_date ?? new Date().toISOString().slice(0, 10),
    due_date: m.due_date ?? null,
    purchase_status: m.purchase_status ?? 'received',
    discount_amount: m.discount_amount ?? 0,
    tax_amount: m.tax_amount ?? 0,
    shipping_amount: m.shipping_amount ?? 0,
    note: m.note ?? '',
    items: m.items?.length
        ? m.items.map((i) => ({
              ingredient_id: i.ingredient_id,
              unit_id: i.unit_id ?? null,
              batch_no: i.batch_no ?? '',
              expiry_date: i.expiry_date ?? null,
              quantity: Number(i.quantity || 0),
              unit_cost: Number(i.unit_cost || 0),
          }))
        : [{ ingredient_id: null, unit_id: null, batch_no: '', expiry_date: null, quantity: 1, unit_cost: 0 }],
});

function addRow() {
    form.items.push({ ingredient_id: null, unit_id: null, batch_no: '', expiry_date: null, quantity: 1, unit_cost: 0 });
}

function removeRow(i) {
    if (form.items.length > 1) form.items.splice(i, 1);
}

const subtotal = computed(() => form.items.reduce((s, r) => s + (Number(r.quantity || 0) * Number(r.unit_cost || 0)), 0));
const grandTotal = computed(() => subtotal.value - Number(form.discount_amount || 0) + Number(form.tax_amount || 0) + Number(form.shipping_amount || 0));

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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('purchases')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('purchases')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('purchases') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <FormField :label="t('supplier')" :error="form.errors.supplier_id">
                            <TomSelectField v-model="form.supplier_id" :options="props.props.suppliers || []" value-field="id" label-field="name" clearable />
                        </FormField>
                    </div>
                    <div class="col-md-4">
                        <FormField :label="t('warehouse')" :error="form.errors.warehouse_id">
                            <TomSelectField v-model="form.warehouse_id" :options="props.props.warehouses || []" value-field="id" label-field="name" clearable />
                        </FormField>
                    </div>
                    <div class="col-md-4">
                        <FormField :label="t('status')" :error="form.errors.purchase_status">
                            <TomSelectField
                                v-model="form.purchase_status"
                                :options="props.props.purchase_statuses || []"
                                value-field="value" label-field="text"
                            />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('purchase_date')" :error="form.errors.purchase_date">
                            <FlatpickrField v-model="form.purchase_date" />
                        </FormField>
                    </div>
                    <div class="col-md-3">
                        <FormField :label="t('due_date')" :error="form.errors.due_date">
                            <FlatpickrField v-model="form.due_date" />
                        </FormField>
                    </div>
                    <div class="col-md-6">
                        <FormField :label="t('note')" :error="form.errors.note">
                            <input v-model="form.note" type="text" class="form-control" />
                        </FormField>
                    </div>
                </div>

                <hr />
                <h6 class="mb-3">{{ t('items') }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 220px;">{{ t('ingredient') }}</th>
                                <th style="min-width: 140px;">{{ t('unit') }}</th>
                                <th>{{ t('batch_no') }}</th>
                                <th style="min-width: 150px;">{{ t('expiry_date') }}</th>
                                <th class="text-end">{{ t('quantity') }}</th>
                                <th class="text-end">{{ t('unit_cost') }}</th>
                                <th class="text-end">{{ t('line_total') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in form.items" :key="i">
                                <td>
                                    <TomSelectField
                                        v-model="row.ingredient_id"
                                        :options="props.props.ingredients || []"
                                        value-field="id" label-field="name"
                                    />
                                </td>
                                <td>
                                    <TomSelectField
                                        v-model="row.unit_id"
                                        :options="props.props.units || []"
                                        value-field="id" label-field="name"
                                        clearable
                                    />
                                </td>
                                <td><input v-model="row.batch_no" type="text" class="form-control form-control-sm" /></td>
                                <td><FlatpickrField v-model="row.expiry_date" /></td>
                                <td><input v-model.number="row.quantity" type="number" min="0" step="0.0001" class="form-control form-control-sm text-end" /></td>
                                <td><input v-model.number="row.unit_cost" type="number" min="0" step="0.0001" class="form-control form-control-sm text-end" /></td>
                                <td class="text-end">{{ ((row.quantity || 0) * (row.unit_cost || 0)).toFixed(2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" :disabled="form.items.length === 1" @click="removeRow(i)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" @click="addRow">
                    <i class="bi bi-plus me-1"></i>{{ t('add_line') }}
                </button>
                <div v-if="form.errors.items" class="text-danger mt-2 small">{{ form.errors.items }}</div>

                <div class="row g-3 mt-3 justify-content-end">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between"><span>{{ t('subtotal') }}</span><span>{{ subtotal.toFixed(2) }}</span></div>
                                <FormField :label="t('discount')" class="mb-1">
                                    <input v-model.number="form.discount_amount" type="number" step="0.01" class="form-control form-control-sm text-end" />
                                </FormField>
                                <FormField :label="t('tax')" class="mb-1">
                                    <input v-model.number="form.tax_amount" type="number" step="0.01" class="form-control form-control-sm text-end" />
                                </FormField>
                                <FormField :label="t('shipping')" class="mb-1">
                                    <input v-model.number="form.shipping_amount" type="number" step="0.01" class="form-control form-control-sm text-end" />
                                </FormField>
                                <div class="d-flex justify-content-between fw-bold border-top pt-2"><span>{{ t('grand_total') }}</span><span class="text-primary">{{ grandTotal.toFixed(2) }}</span></div>
                            </div>
                        </div>
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
