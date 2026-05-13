<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import YajraDataTable from '@/Components/YajraDataTable.vue';
import FormField from '@/Components/FormField.vue';
import TomSelectField from '@/Components/TomSelectField.vue';

const props = defineProps({
    titleKey: { type: String, required: true },
    routes: { type: Object, required: true },
    columns: { type: Array, required: true },
    activeShift: { type: Object, default: null },
    counters: { type: Array, default: () => [] },
});

const { t } = useI18n();
const refreshKey = ref(0);

function renderColumns() {
    return props.columns.map((c) => ({
        data: c.data,
        name: c.name,
        title: c.title,
        render: (d) => (d === null || d === undefined ? '' : String(d)),
    }));
}

const openForm = useForm({
    opening_cash: 0,
    pos_counter_id: null,
    note: '',
});

const closeForm = useForm({
    closing_cash: 0,
    note: '',
});

function openShift() {
    openForm.post(route(props.routes.open), {
        preserveScroll: true,
        onSuccess: () => { refreshKey.value++; },
    });
}

function closeShift() {
    if (!props.activeShift) return;
    closeForm.post(route(props.routes.close, props.activeShift.id), {
        preserveScroll: true,
        onSuccess: () => { refreshKey.value++; },
    });
}
</script>

<template>
    <AdminLayout :title="t(titleKey.replace('coffee.', ''))" :page-title="t(titleKey.replace('coffee.', ''))">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ activeShift ? t('close_shift') : t('open_shift') }}</h5>
                    </div>
                    <div class="card-body">
                        <form v-if="!activeShift" @submit.prevent="openShift">
                            <FormField :label="t('opening_cash')" :error="openForm.errors.opening_cash">
                                <input v-model.number="openForm.opening_cash" type="number" step="0.01" class="form-control" required />
                            </FormField>
                            <FormField :label="t('pos_counters')">
                                <TomSelectField
                                    v-model="openForm.pos_counter_id"
                                    :options="counters"
                                    value-field="id"
                                    label-field="name"
                                    clearable
                                />
                            </FormField>
                            <FormField :label="t('note')">
                                <textarea v-model="openForm.note" class="form-control" rows="2"></textarea>
                            </FormField>
                            <button class="btn btn-primary" type="submit" :disabled="openForm.processing">
                                <i class="bi bi-play-circle me-1"></i>{{ t('open_shift') }}
                            </button>
                        </form>
                        <form v-else @submit.prevent="closeShift">
                            <div class="alert alert-info py-2 mb-3">
                                {{ t('active_shift') }}: <strong>{{ activeShift.shift_no }}</strong>
                            </div>
                            <FormField :label="t('closing_cash')" :error="closeForm.errors.closing_cash">
                                <input v-model.number="closeForm.closing_cash" type="number" step="0.01" class="form-control" required />
                            </FormField>
                            <FormField :label="t('note')">
                                <textarea v-model="closeForm.note" class="form-control" rows="2"></textarea>
                            </FormField>
                            <button class="btn btn-danger" type="submit" :disabled="closeForm.processing">
                                <i class="bi bi-stop-circle me-1"></i>{{ t('close_shift') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ t('cashier_shifts') }}</h5></div>
            <div class="card-body">
                <YajraDataTable :ajax-url="route(routes.data)" :columns="renderColumns()" :refresh-key="refreshKey" />
            </div>
        </div>
    </AdminLayout>
</template>
