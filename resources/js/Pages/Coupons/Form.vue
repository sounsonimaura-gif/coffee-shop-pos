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
        code: model.code ?? null,
        description: model.description ?? null,
        discount_type: model.discount_type ?? 'percentage',
        discount_value: model.discount_value ?? 0,
        min_purchase_amount: model.min_purchase_amount ?? 0,
        usage_limit: model.usage_limit ?? null,
        start_at: model.start_at ?? null,
        end_at: model.end_at ?? null,
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

                <FormField :label="t('code')" :error="form.errors.code" :required="!!'required'">
                    <input v-model="form.code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('description')" :error="form.errors.description">
                    <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </FormField>
                <FormField :label="t('type')" :error="form.errors.discount_type" :required="!!''">
                    <TomSelectField v-model="form.discount_type" :options='[{"value":"percentage","text":"Percentage"},{"value":"fixed","text":"Fixed Amount"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('discount')" :error="form.errors.discount_value" :required="!!''">
                    <input v-model="form.discount_value" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('price')" :error="form.errors.min_purchase_amount" :required="!!''">
                    <input v-model="form.min_purchase_amount" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('quantity')" :error="form.errors.usage_limit" :required="!!''">
                    <input v-model="form.usage_limit" type="number" step="1" class="form-control" />
                </FormField>
                <FormField :label="t('from')" :error="form.errors.start_at">
                    <FlatpickrField v-model="form.start_at" :enable-time="true" date-format="Y-m-d H:i" />
                </FormField>
                <FormField :label="t('to')" :error="form.errors.end_at">
                    <FlatpickrField v-model="form.end_at" :enable-time="true" date-format="Y-m-d H:i" />
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