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
        branch_id: model.branch_id ?? null,
        floor_id: model.floor_id ?? null,
        zone_id: model.zone_id ?? null,
        table_no: model.table_no ?? null,
        name: model.name ?? null,
        capacity: model.capacity ?? 4,
        status: model.status ?? 'available',
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

                <FormField :label="t('branch')" :error="form.errors.branch_id">
                    <TomSelectField v-model="form.branch_id" :options="props.props.branches || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('table_floors')" :error="form.errors.floor_id">
                    <TomSelectField v-model="form.floor_id" :options="props.props.floors || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('table_zones')" :error="form.errors.zone_id">
                    <TomSelectField v-model="form.zone_id" :options="props.props.zones || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.table_no" :required="!!'required'">
                    <input v-model="form.table_no" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('name')" :error="form.errors.name" :required="!!''">
                    <input v-model="form.name" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('quantity')" :error="form.errors.capacity" :required="!!''">
                    <input v-model="form.capacity" type="number" step="1" class="form-control" />
                </FormField>
                <FormField :label="t('status')" :error="form.errors.status" :required="!!''">
                    <TomSelectField v-model="form.status" :options='[{"value":"available","text":"Available"},{"value":"occupied","text":"Occupied"},{"value":"reserved","text":"Reserved"},{"value":"closed","text":"Closed"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
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