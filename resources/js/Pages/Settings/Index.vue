<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    settings: { type: Array, default: () => [] },
});

const { t } = useI18n();

const form = useForm({
    settings: props.settings.map((s) => ({ id: s.id, key: s.key, group: s.group, value: s.value })),
});

function submit() {
    form.put(route('admin.settings.update'));
}
</script>

<template>
    <AdminLayout :title="t('settings')" :page-title="t('settings')">
        <form class="card" @submit.prevent="submit">
            <div class="card-header"><h5 class="mb-0">{{ t('settings') }}</h5></div>
            <div class="card-body">
                <div v-if="!form.settings.length" class="text-muted text-center py-4">
                    {{ t('no_data') }}
                </div>
                <div v-else class="row g-3">
                    <div v-for="(setting, i) in form.settings" :key="setting.id" class="col-md-6">
                        <FormField :label="setting.key" :hint="setting.group">
                            <input v-model="form.settings[i].value" type="text" class="form-control" />
                        </FormField>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit" :disabled="form.processing">
                    <i class="bi bi-check2 me-1"></i>{{ t('save') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
