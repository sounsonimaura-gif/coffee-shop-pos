<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const { t } = useI18n();

const form = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
    phone: props.user.phone ?? '',
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

function submit() {
    form.put(route('admin.profile.update'), {
        onSuccess: () => form.reset('current_password', 'new_password', 'new_password_confirmation'),
    });
}
</script>

<template>
    <AdminLayout :title="t('profile')" :page-title="t('profile')">
        <form class="card" @submit.prevent="submit">
            <div class="card-header"><h5 class="mb-0">{{ t('profile') }}</h5></div>
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
                </div>
                <div class="col-md-6">
                    <h6>{{ t('change_password') }}</h6>
                    <FormField :label="t('current_password')" :error="form.errors.current_password">
                        <input v-model="form.current_password" type="password" class="form-control" />
                    </FormField>
                    <FormField :label="t('new_password')" :error="form.errors.new_password">
                        <input v-model="form.new_password" type="password" class="form-control" />
                    </FormField>
                    <FormField :label="t('confirm_password')">
                        <input v-model="form.new_password_confirmation" type="password" class="form-control" />
                    </FormField>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" :disabled="form.processing">
                    <i class="bi bi-check2 me-1"></i>{{ t('save') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
