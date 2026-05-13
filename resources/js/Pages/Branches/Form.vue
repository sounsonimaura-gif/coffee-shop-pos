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
        branch_code: model.branch_code ?? null,
        name: model.name ?? null,
        phone: model.phone ?? null,
        address: model.address ?? null,
        open_time: model.open_time ?? null,
        close_time: model.close_time ?? null,
        is_main_branch: model.is_main_branch ?? false,
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

                <FormField :label="t('branch_code')" :error="form.errors.branch_code" :required="!!'required'">
                    <input v-model="form.branch_code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('phone')" :error="form.errors.phone" :required="!!''">
                    <input v-model="form.phone" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('address')" :error="form.errors.address">
                    <textarea v-model="form.address" class="form-control" rows="2"></textarea>
                </FormField>
                <FormField :label="t('open_time')" :error="form.errors.open_time">
                    <FlatpickrField v-model="form.open_time" :time-only="true" />
                </FormField>
                <FormField :label="t('close_time')" :error="form.errors.close_time">
                    <FlatpickrField v-model="form.close_time" :time-only="true" />
                </FormField>
                <FormField :label="t('is_main_branch')">
                    <div class="form-check form-switch">
                        <input v-model="form.is_main_branch" type="checkbox" class="form-check-input" :id="'switch-is_main_branch'" />
                        <label class="form-check-label" :for="'switch-is_main_branch'">{{ form.is_main_branch ? t('yes') : t('no') }}</label>
                    </div>
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