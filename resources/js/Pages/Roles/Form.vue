<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';

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
    name: m.name ?? '',
    description: m.description ?? '',
    is_active: m.is_active ?? true,
    permission_ids: m.permission_ids ?? [],
});

function toggle(permId) {
    const idx = form.permission_ids.indexOf(permId);
    if (idx >= 0) form.permission_ids.splice(idx, 1);
    else form.permission_ids.push(permId);
}

function toggleModule(moduleKey) {
    const ids = (props.props.permissions[moduleKey] || []).map((p) => p.id);
    const allSelected = ids.every((id) => form.permission_ids.includes(id));
    if (allSelected) {
        form.permission_ids = form.permission_ids.filter((id) => !ids.includes(id));
    } else {
        form.permission_ids = Array.from(new Set([...form.permission_ids, ...ids]));
    }
}

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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('roles')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('roles')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('roles') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <FormField :label="t('name')" :error="form.errors.name" required>
                            <input v-model="form.name" type="text" class="form-control" />
                        </FormField>
                    </div>
                    <div class="col-md-8">
                        <FormField :label="t('description')" :error="form.errors.description">
                            <input v-model="form.description" type="text" class="form-control" />
                        </FormField>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-4">
                            <input v-model="form.is_active" class="form-check-input" type="checkbox" id="is_active" />
                            <label class="form-check-label" for="is_active">{{ t('active') }}</label>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3">{{ t('permissions') }}</h6>
                <div class="row g-3">
                    <div v-for="(perms, moduleKey) in props.props.permissions" :key="moduleKey" class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between py-2">
                                <strong>{{ moduleKey }}</strong>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="toggleModule(moduleKey)">
                                    {{ t('toggle_all') }}
                                </button>
                            </div>
                            <div class="card-body py-2">
                                <div v-for="perm in perms" :key="perm.id" class="form-check">
                                    <input
                                        :id="'perm-' + perm.id"
                                        type="checkbox"
                                        class="form-check-input"
                                        :checked="form.permission_ids.includes(perm.id)"
                                        @change="toggle(perm.id)"
                                    />
                                    <label :for="'perm-' + perm.id" class="form-check-label">
                                        {{ perm.label || perm.name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
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
