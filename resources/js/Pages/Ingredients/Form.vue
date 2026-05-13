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

const model = props.model || {};

const form = useForm({
        category_id: model.category_id ?? null,
        unit_id: model.unit_id ?? null,
        ingredient_code: model.ingredient_code ?? null,
        name: model.name ?? null,
        sku: model.sku ?? null,
        min_stock_level: model.min_stock_level ?? 0,
        reorder_level: model.reorder_level ?? 0,
        cost_per_unit: model.cost_per_unit ?? 0,
        is_active: model.is_active ?? true,
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t(titleKey.replace('coffee.', ''))"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t(titleKey.replace('coffee.', ''))"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t(titleKey.replace('coffee.', '')) }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row">
                <div class="col-md-8">

                <FormField :label="t('ingredient_categories')" :error="form.errors.category_id">
                    <TomSelectField v-model="form.category_id" :options="props.props.ingredient_categories || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('units')" :error="form.errors.unit_id">
                    <TomSelectField v-model="form.unit_id" :options="props.props.units || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.ingredient_code" :required="!!'required'">
                    <input v-model="form.ingredient_code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.sku" :required="!!''">
                    <input v-model="form.sku" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('quantity')" :error="form.errors.min_stock_level" :required="!!''">
                    <input v-model="form.min_stock_level" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('quantity')" :error="form.errors.reorder_level" :required="!!''">
                    <input v-model="form.reorder_level" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('cost_price')" :error="form.errors.cost_per_unit" :required="!!''">
                    <input v-model="form.cost_per_unit" type="number" step="0.0001" class="form-control" />
                </FormField>
                <FormField :label="t('is_active')">
                    <div class="form-check form-switch">
                        <input v-model="form.is_active" type="checkbox" class="form-check-input" :id="'switch-is_active'" />
                        <label class="form-check-label" :for="'switch-is_active'">{{ form.is_active ? t('yes') : t('no') }}</label>
                    </div>
                </FormField>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="bi bi-save me-1"></i>{{ t('save') }}
                </button>
                <Link :href="route(routes.index)" class="btn btn-outline-secondary">{{ t('cancel') }}</Link>
            </div>
        </form>
    </AdminLayout>
</template>