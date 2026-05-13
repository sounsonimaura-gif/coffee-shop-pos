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
        membership_level_id: model.membership_level_id ?? null,
        customer_code: model.customer_code ?? null,
        name: model.name ?? null,
        phone: model.phone ?? null,
        email: model.email ?? null,
        address: model.address ?? null,
        gender: model.gender ?? null,
        birthday: model.birthday ?? null,
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

                <FormField :label="t('membership_levels')" :error="form.errors.membership_level_id">
                    <TomSelectField v-model="form.membership_level_id" :options="props.props.membership_levels || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.customer_code" :required="!!'required'">
                    <input v-model="form.customer_code" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('name')" :error="form.errors.name" :required="!!'required'">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('phone')" :error="form.errors.phone" :required="!!''">
                    <input v-model="form.phone" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('email')" :error="form.errors.email" :required="!!''">
                    <input v-model="form.email" type="email" class="form-control" />
                </FormField>
                <FormField :label="t('address')" :error="form.errors.address">
                    <textarea v-model="form.address" class="form-control" rows="2"></textarea>
                </FormField>
                <FormField :label="t('gender')" :error="form.errors.gender" :required="!!''">
                    <TomSelectField v-model="form.gender" :options='[{"value":"male","text":"Male"},{"value":"female","text":"Female"},{"value":"other","text":"Other"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('birthday')" :error="form.errors.birthday" :required="!!''">
                    <FlatpickrField v-model="form.birthday" date-format="Y-m-d" />
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