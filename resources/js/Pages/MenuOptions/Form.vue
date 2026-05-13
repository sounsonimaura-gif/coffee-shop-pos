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
        name: model.name ?? null,
        is_required: model.is_required ?? false,
        allow_multiple: model.allow_multiple ?? false,
        sort_order: model.sort_order ?? 0,
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

                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('is_active')">
                    <div class="form-check form-switch">
                        <input v-model="form.is_required" type="checkbox" class="form-check-input" :id="'switch-is_required'" />
                        <label class="form-check-label" :for="'switch-is_required'">{{ form.is_required ? t('yes') : t('no') }}</label>
                    </div>
                </FormField>
                <FormField :label="t('is_active')">
                    <div class="form-check form-switch">
                        <input v-model="form.allow_multiple" type="checkbox" class="form-check-input" :id="'switch-allow_multiple'" />
                        <label class="form-check-label" :for="'switch-allow_multiple'">{{ form.allow_multiple ? t('yes') : t('no') }}</label>
                    </div>
                </FormField>
                <FormField :label="t('sort_order')" :error="form.errors.sort_order" :required="!!''">
                    <input v-model="form.sort_order" type="number" step="1" class="form-control" />
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