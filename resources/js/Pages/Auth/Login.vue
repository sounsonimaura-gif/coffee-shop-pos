<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import FlashMessages from '@/Components/FlashMessages.vue';

const { t, locale, setLocale, availableLocales } = useI18n();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'));
}
</script>

<template>
    <Head :title="t('login')" />
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="card shadow-sm" style="width: 28rem">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="mb-0">{{ t('app_name') }}</h4>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-translate me-1"></i>{{ locale === 'kh' ? t('khmer') : t('english') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li v-for="loc in availableLocales" :key="loc">
                                <a class="dropdown-item" :class="{ active: locale === loc }" href="#" @click.prevent="setLocale(loc)">
                                    {{ loc === 'kh' ? t('khmer') : t('english') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <p class="text-muted mb-4">{{ t('sign_in_subtitle') }}</p>

                <FlashMessages />

                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">{{ t('email') }}</label>
                        <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" required />
                        <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ t('password') }}</label>
                        <input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': form.errors.password }" required />
                        <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                    </div>
                    <div class="form-check mb-3">
                        <input v-model="form.remember" type="checkbox" class="form-check-input" id="remember" />
                        <label for="remember" class="form-check-label">{{ t('remember_me') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ t('sign_in') }}
                    </button>
                </form>
                <p class="small text-muted mt-3 mb-0">
                    admin@coffee.test / password
                </p>
            </div>
        </div>
    </div>
</template>
