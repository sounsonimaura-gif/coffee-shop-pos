<script setup>
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
const m = props.model || {};

const form = useForm({
    staff_code: m.staff_code ?? '',
    name: m.name ?? '',
    phone: m.phone ?? '',
    email: m.email ?? '',
    position: m.position ?? '',
    salary: m.salary ?? 0,
    hire_date: m.hire_date ?? null,
    branch_id: m.branch_id ?? null,
    user_id: m.user_id ?? null,
    status: m.status ?? 'active',
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('staff')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('staff')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('staff') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <FormField :label="t('code')" :error="form.errors.staff_code" required>
                        <input v-model="form.staff_code" type="text" class="form-control" />
                    </FormField>
                    <FormField :label="t('name')" :error="form.errors.name" required>
                        <input v-model="form.name" type="text" class="form-control" />
                    </FormField>
                    <FormField :label="t('phone')" :error="form.errors.phone">
                        <input v-model="form.phone" type="text" class="form-control" />
                    </FormField>
                    <FormField :label="t('email')" :error="form.errors.email">
                        <input v-model="form.email" type="email" class="form-control" />
                    </FormField>
                    <FormField :label="t('position')" :error="form.errors.position">
                        <input v-model="form.position" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-6">
                    <FormField :label="t('salary')" :error="form.errors.salary">
                        <input v-model.number="form.salary" type="number" step="0.01" class="form-control" />
                    </FormField>
                    <FormField :label="t('hire_date')" :error="form.errors.hire_date">
                        <FlatpickrField v-model="form.hire_date" />
                    </FormField>
                    <FormField :label="t('branch')" :error="form.errors.branch_id">
                        <TomSelectField v-model="form.branch_id" :options="props.props.branches || []" value-field="id" label-field="name" clearable />
                    </FormField>
                    <FormField :label="t('linked_user')" :error="form.errors.user_id">
                        <TomSelectField v-model="form.user_id" :options="props.props.users || []" value-field="id" label-field="name" clearable />
                    </FormField>
                    <FormField :label="t('status')" :error="form.errors.status">
                        <TomSelectField v-model="form.status" :options="props.props.statuses || []" value-field="value" label-field="text" />
                    </FormField>
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
