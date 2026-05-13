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
        expense_category_id: model.expense_category_id ?? null,
        payment_method_id: model.payment_method_id ?? null,
        branch_id: model.branch_id ?? null,
        expense_no: model.expense_no ?? null,
        expense_date: model.expense_date ?? null,
        amount: model.amount ?? null,
        reference_no: model.reference_no ?? null,
        notes: model.notes ?? null,
        status: model.status ?? 'pending',
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

                <FormField :label="t('expense_categories')" :error="form.errors.expense_category_id">
                    <TomSelectField v-model="form.expense_category_id" :options="props.props.expense_categories || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('payment_methods')" :error="form.errors.payment_method_id">
                    <TomSelectField v-model="form.payment_method_id" :options="props.props.payment_methods || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('branch')" :error="form.errors.branch_id">
                    <TomSelectField v-model="form.branch_id" :options="props.props.branches || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.expense_no" :required="!!'required'">
                    <input v-model="form.expense_no" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('from')" :error="form.errors.expense_date" :required="!!'required'">
                    <FlatpickrField v-model="form.expense_date" date-format="Y-m-d" />
                </FormField>
                <FormField :label="t('price')" :error="form.errors.amount" :required="!!'required'">
                    <input v-model="form.amount" type="number" step="0.01" class="form-control" />
                </FormField>
                <FormField :label="t('code')" :error="form.errors.reference_no" :required="!!''">
                    <input v-model="form.reference_no" type="text" class="form-control" />
                </FormField>
                <FormField :label="t('description')" :error="form.errors.notes">
                    <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
                </FormField>
                <FormField :label="t('status')" :error="form.errors.status" :required="!!''">
                    <TomSelectField v-model="form.status" :options='[{"value":"pending","text":"Pending"},{"value":"approved","text":"Approved"},{"value":"paid","text":"Paid"}]' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
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