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
    menu_item_id: m.menu_item_id ?? null,
    menu_size_id: m.menu_size_id ?? null,
    name: m.name ?? '',
    is_default: m.is_default ?? false,
    is_active: m.is_active ?? true,
    items: m.items?.length
        ? m.items.map((i) => ({
              ingredient_id: i.ingredient_id,
              unit_id: i.unit_id ?? null,
              quantity_used: Number(i.quantity_used || 0),
              cost_per_unit: Number(i.cost_per_unit || 0),
              is_required: i.is_required ?? true,
              deduct_stock: i.deduct_stock ?? true,
          }))
        : [{ ingredient_id: null, unit_id: null, quantity_used: 1, cost_per_unit: 0, is_required: true, deduct_stock: true }],
});

const totalCost = computed(() => form.items.reduce((s, r) => s + Number(r.quantity_used || 0) * Number(r.cost_per_unit || 0), 0));

function addRow() {
    form.items.push({ ingredient_id: null, unit_id: null, quantity_used: 1, cost_per_unit: 0, is_required: true, deduct_stock: true });
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('recipes')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('recipes')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('recipes') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <FormField :label="t('menu_item')" :error="form.errors.menu_item_id" required>
                            <TomSelectField v-model="form.menu_item_id" :options="props.props.menu_items || []" value-field="id" label-field="name" />
                        </FormField>
                    </div>
                    <div class="col-md-4">
                        <FormField :label="t('size')" :error="form.errors.menu_size_id">
                            <TomSelectField v-model="form.menu_size_id" :options="props.props.menu_sizes || []" value-field="id" label-field="name" clearable />
                        </FormField>
                    </div>
                    <div class="col-md-4">
                        <FormField :label="t('name')" :error="form.errors.name">
                            <input v-model="form.name" type="text" class="form-control" />
                        </FormField>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check me-3">
                            <input id="is_default" v-model="form.is_default" type="checkbox" class="form-check-input" />
                            <label class="form-check-label" for="is_default">{{ t('default') }}</label>
                        </div>
                        <div class="form-check">
                            <input id="is_active" v-model="form.is_active" type="checkbox" class="form-check-input" />
                            <label class="form-check-label" for="is_active">{{ t('is_active') }}</label>
                        </div>
                    </div>
                </div>

                <hr />
                <h6>{{ t('ingredients') }}</h6>
                <table class="table table-sm align-middle">
                    <thead><tr>
                        <th style="min-width: 220px;">{{ t('ingredient') }}</th>
                        <th style="min-width: 140px;">{{ t('unit') }}</th>
                        <th class="text-end" style="width: 130px;">{{ t('quantity_used') }}</th>
                        <th class="text-end" style="width: 130px;">{{ t('cost_per_unit') }}</th>
                        <th class="text-end" style="width: 120px;">{{ t('total_cost') }}</th>
                        <th style="width: 70px;">{{ t('required') }}</th>
                        <th style="width: 90px;">{{ t('deduct_stock') }}</th>
                        <th></th>
                    </tr></thead>
                    <tbody>
                        <tr v-for="(row, i) in form.items" :key="i">
                            <td><TomSelectField v-model="row.ingredient_id" :options="props.props.ingredients || []" value-field="id" label-field="name" /></td>
                            <td><TomSelectField v-model="row.unit_id" :options="props.props.units || []" value-field="id" label-field="name" clearable /></td>
                            <td><input v-model.number="row.quantity_used" type="number" min="0" step="0.0001" class="form-control form-control-sm text-end" /></td>
                            <td><input v-model.number="row.cost_per_unit" type="number" min="0" step="0.0001" class="form-control form-control-sm text-end" /></td>
                            <td class="text-end">{{ ((row.quantity_used || 0) * (row.cost_per_unit || 0)).toFixed(4) }}</td>
                            <td class="text-center"><input v-model="row.is_required" type="checkbox" class="form-check-input" /></td>
                            <td class="text-center"><input v-model="row.deduct_stock" type="checkbox" class="form-check-input" /></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" :disabled="form.items.length === 1" @click="removeRow(i)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="4" class="text-end">{{ t('estimated_cost') }}</th><th class="text-end">{{ totalCost.toFixed(4) }}</th><th colspan="3"></th></tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" @click="addRow">
                    <i class="bi bi-plus me-1"></i>{{ t('add_line') }}
                </button>
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
