<script setup>
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

const model = props.model || {};

const form = useForm({
    name: model.name ?? '',
    email: model.email ?? '',
    phone: model.phone ?? '',
    role_id: model.role_id ?? null,
    default_branch_id: model.default_branch_id ?? null,
    password: '',
    status: model.status ?? 'active',
    branch_ids: model.branch_ids ?? [],
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('users')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('users')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('users') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <FormField :label="t('name')" :error="form.errors.name" required>
                        <input v-model="form.name" type="text" class="form-control" />
                    </FormField>
                    <FormField :label="t('email')" :error="form.errors.email" required>
                        <input v-model="form.email" type="email" class="form-control" />
                    </FormField>
                    <FormField :label="t('phone')" :error="form.errors.phone">
                        <input v-model="form.phone" type="text" class="form-control" />
                    </FormField>
                    <FormField :label="t(isEdit ? 'change_password' : 'password')" :error="form.errors.password" :required="!isEdit">
                        <input v-model="form.password" type="password" class="form-control" :placeholder="isEdit ? t('leave_blank_to_keep') : ''" />
                    </FormField>
                </div>
                <div class="col-md-6">
                    <FormField :label="t('role')" :error="form.errors.role_id" required>
                        <TomSelectField v-model="form.role_id" :options="props.props.roles || []" value-field="id" label-field="name" />
                    </FormField>
                    <FormField :label="t('default_branch')" :error="form.errors.default_branch_id">
                        <TomSelectField v-model="form.default_branch_id" :options="props.props.branches || []" value-field="id" label-field="name" clearable />
                    </FormField>
                    <FormField :label="t('branches')" :error="form.errors.branch_ids">
                        <TomSelectField v-model="form.branch_ids" :options="props.props.branches || []" value-field="id" label-field="name" multiple />
                    </FormField>
                    <FormField :label="t('status')" :error="form.errors.status" required>
                        <TomSelectField
                            v-model="form.status"
                            :options="props.props.statuses || [{value:'active',text:'Active'},{value:'inactive',text:'Inactive'},{value:'blocked',text:'Blocked'}]"
                            value-field="value" label-field="text"
                        />
                    </FormField>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="bi bi-check2 me-1"></i>{{ isEdit ? t('update') : t('save') }}
                </button>
                <Link :href="route(routes.index)" class="btn btn-outline-secondary">{{ t('cancel') }}</Link>
            </div>
        </form>
    </AdminLayout>
</template>
