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
        company_code: model.company_code ?? null,
        name: model.name ?? null,
        owner_name: model.owner_name ?? null,
        phone: model.phone ?? null,
        email: model.email ?? null,
        website: model.website ?? null,
        address: model.address ?? null,
        tax_no: model.tax_no ?? null,
        currency_code: model.currency_code ?? 'USD',
        language_code: model.language_code ?? 'en',
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

                <FormField :label="t('company_code')" :error="form.errors.company_code" :required="!!'required'">
                    <input v-model="form.company_code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('owner_name')" :error="form.errors.owner_name" :required="!!''">
                    <input v-model="form.owner_name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('phone')" :error="form.errors.phone" :required="!!''">
                    <input v-model="form.phone" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('email')" :error="form.errors.email" :required="!!''">
                    <input v-model="form.email" type="email" class="form-control" />
                </FormField>
                <FormField :label="t('website')" :error="form.errors.website" :required="!!''">
                    <input v-model="form.website" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('address')" :error="form.errors.address">
                    <textarea v-model="form.address" class="form-control" rows="2"></textarea>
                </FormField>
                <FormField :label="t('tax_no')" :error="form.errors.tax_no" :required="!!''">
                    <input v-model="form.tax_no" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('currency_code')" :error="form.errors.currency_code" :required="!!''">
                    <input v-model="form.currency_code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('language_code')" :error="form.errors.language_code" :required="!!''">
                    <TomSelectField v-model="form.language_code" :options='[{"value":"en","text":"English"},{"value":"kh","text":"Khmer"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
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