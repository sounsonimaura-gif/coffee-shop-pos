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
        name: model.name ?? null,
        type: model.type ?? 'cash',
        account_no: model.account_no ?? null,
        is_default: model.is_default ?? false,
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
                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('type')" :error="form.errors.type" :required="!!''">
                    <TomSelectField v-model="form.type" :options='[{"value":"cash","text":"Cash"},{"value":"card","text":"Card"},{"value":"bank","text":"Bank"},{"value":"qr","text":"QR"},{"value":"wallet","text":"Wallet"},{"value":"other","text":"Other"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.account_no" :required="!!''">
                    <input v-model="form.account_no" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('is_active')">
                    <div class="form-check form-switch">
                        <input v-model="form.is_default" type="checkbox" class="form-check-input" :id="'switch-is_default'" />
                        <label class="form-check-label" :for="'switch-is_default'">{{ form.is_default ? t('yes') : t('no') }}</label>
                    </div>
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