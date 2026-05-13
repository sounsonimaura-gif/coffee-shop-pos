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
const m = props.model || {};

const form = useForm({
    template_key: m.template_key ?? '',
    title: m.title ?? '',
    body: m.body ?? '',
    channel: m.channel ?? 'system',
    is_active: m.is_active ?? true,
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('notification_templates')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('notification_templates')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('notification_templates') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <FormField :label="t('code')" :error="form.errors.template_key" required>
                        <input v-model="form.template_key" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-6">
                    <FormField :label="t('title')" :error="form.errors.title" required>
                        <input v-model="form.title" type="text" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-4">
                    <FormField :label="t('channel')" :error="form.errors.channel" required>
                        <TomSelectField v-model="form.channel" :options="props.props.channels || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input id="is_active" v-model="form.is_active" type="checkbox" class="form-check-input" />
                        <label class="form-check-label" for="is_active">{{ t('is_active') }}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <FormField :label="t('body')" :error="form.errors.body">
                        <textarea v-model="form.body" class="form-control" rows="6" />
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
