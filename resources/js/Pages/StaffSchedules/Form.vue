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
    staff_id: m.staff_id ?? null,
    branch_id: m.branch_id ?? null,
    day_of_week: m.day_of_week ?? 1,
    start_time: m.start_time ?? '09:00',
    end_time: m.end_time ?? '17:00',
    is_day_off: m.is_day_off ?? false,
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
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t('staff_schedules')"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t('staff_schedules')"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t('staff_schedules') }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <FormField :label="t('staff')" :error="form.errors.staff_id" required>
                        <TomSelectField v-model="form.staff_id" :options="props.props.staff || []" value-field="id" label-field="name" />
                    </FormField>
                </div>
                <div class="col-md-6">
                    <FormField :label="t('branch')" :error="form.errors.branch_id">
                        <TomSelectField v-model="form.branch_id" :options="props.props.branches || []" value-field="id" label-field="name" clearable />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('day')" :error="form.errors.day_of_week" required>
                        <TomSelectField v-model="form.day_of_week" :options="props.props.days || []" value-field="value" label-field="text" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('start_time')" :error="form.errors.start_time">
                        <input v-model="form.start_time" type="time" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-3">
                    <FormField :label="t('end_time')" :error="form.errors.end_time">
                        <input v-model="form.end_time" type="time" class="form-control" />
                    </FormField>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input id="is_day_off" v-model="form.is_day_off" type="checkbox" class="form-check-input" />
                        <label class="form-check-label" for="is_day_off">{{ t('day_off') }}</label>
                    </div>
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
