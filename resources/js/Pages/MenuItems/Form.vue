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
        menu_code: model.menu_code ?? null,
        name: model.name ?? null,
        description: model.description ?? null,
        base_price: model.base_price ?? null,
        cost_price: model.cost_price ?? null,
        sale_price: model.sale_price ?? null,
        preparation_time_minutes: model.preparation_time_minutes ?? 0,
        is_featured: model.is_featured ?? false,
        is_best_seller: model.is_best_seller ?? false,
        availability_status: model.availability_status ?? 'available',
        status: model.status ?? 'active',
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

                <FormField :label="t('menu_categories')" :error="form.errors.category_id">
                    <TomSelectField v-model="form.category_id" :options="props.props.menu_categories || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.menu_code" :required="!!'required'">
                    <input v-model="form.menu_code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('description')" :error="form.errors.description">
                    <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </FormField>
                <FormField :label="t('price')" :error="form.errors.base_price" :required="!!''">
                    <input v-model="form.base_price" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('cost_price')" :error="form.errors.cost_price" :required="!!''">
                    <input v-model="form.cost_price" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('price')" :error="form.errors.sale_price" :required="!!''">
                    <input v-model="form.sale_price" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('prep_time_minutes')" :error="form.errors.preparation_time_minutes" :required="!!''">
                    <input v-model="form.preparation_time_minutes" type="number" step="1" class="form-control" />
                </FormField>
                <FormField :label="t('is_featured')">
                    <div class="form-check form-switch">
                        <input v-model="form.is_featured" type="checkbox" class="form-check-input" :id="'switch-is_featured'" />
                        <label class="form-check-label" :for="'switch-is_featured'">{{ form.is_featured ? t('yes') : t('no') }}</label>
                    </div>
                </FormField>
                <FormField :label="t('is_best_seller')">
                    <div class="form-check form-switch">
                        <input v-model="form.is_best_seller" type="checkbox" class="form-check-input" :id="'switch-is_best_seller'" />
                        <label class="form-check-label" :for="'switch-is_best_seller'">{{ form.is_best_seller ? t('yes') : t('no') }}</label>
                    </div>
                </FormField>
                <FormField :label="t('status')" :error="form.errors.availability_status" :required="!!''">
                    <TomSelectField v-model="form.availability_status" :options='[{"value":"available","text":"Available"},{"value":"unavailable","text":"Unavailable"},{"value":"sold_out","text":"Sold out"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('status')" :error="form.errors.status" :required="!!''">
                    <TomSelectField v-model="form.status" :options='[{"value":"active","text":"Active"},{"value":"inactive","text":"Inactive"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
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