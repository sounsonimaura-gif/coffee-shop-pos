<script setup>
import { computed, inject } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useI18n, setLocale } from '@/composables/useI18n.js';

const page = usePage();
const { t, locale } = useI18n();

const layout = inject('admin-layout', null);

const user = computed(() => page.props.auth?.user || null);
const activeBranchId = computed(() => page.props.auth?.active_branch_id);
const branches = computed(() => user.value?.branches || []);
const activeBranch = computed(() =>
    branches.value.find((b) => b.id === activeBranchId.value) || branches.value[0] || null
);

const userInitials = computed(() => {
    const name = user.value?.name || 'U';
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((p) => p[0].toUpperCase())
        .join('');
});

function toggleSidebar() {
    if (layout?.toggleSidebar) layout.toggleSidebar();
}

function logout() {
    router.post(route('logout'));
}

function changeLocale(loc) {
    setLocale(loc);
}

function switchBranch(branchId) {
    router.post(
        route('branch.switch'),
        { branch_id: branchId },
        { preserveScroll: true }
    );
}
</script>

<template>
    <header class="top-header">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-icon" @click="toggleSidebar" role="button" :aria-label="t('toggle_sidebar')">
                <i class="bi bi-list"></i>
            </div>
            <div class="top-navbar d-none d-md-block">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <Link class="nav-link" :href="route('admin.dashboard')">{{ t('dashboard') }}</Link>
                    </li>
                    <li class="nav-item dropdown" v-if="activeBranch">
                        <a
                            class="nav-link dropdown-toggle dropdown-toggle-nocaret has-caret"
                            href="#"
                            data-bs-toggle="dropdown"
                        >
                            <i class="bi bi-shop"></i>
                            <span class="fw-medium">{{ activeBranch.name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header">{{ t('switch_branch') }}</li>
                            <li v-for="b in branches" :key="b.id">
                                <button
                                    class="dropdown-item"
                                    :class="{ active: b.id === activeBranchId }"
                                    type="button"
                                    @click="switchBranch(b.id)"
                                >
                                    {{ b.name }}
                                    <small class="ms-2">{{ b.code }}</small>
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <form class="searchbar d-none d-xl-flex ms-auto" @submit.prevent>
                <div class="position-absolute top-50 translate-middle-y search-icon ms-3">
                    <i class="bi bi-search"></i>
                </div>
                <input class="form-control" type="text" :placeholder="t('search')" />
            </form>

            <div class="top-navbar-right ms-3">
                <ul class="navbar-nav align-items-center">
                    <!-- Language switcher (no full page reload) -->
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle dropdown-toggle-nocaret has-caret"
                            href="#"
                            data-bs-toggle="dropdown"
                            :aria-label="t('language')"
                        >
                            <i class="bi bi-globe2"></i>
                            <span class="text-uppercase fw-medium">{{ locale }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item" :class="{ active: locale === 'en' }" type="button" @click="changeLocale('en')">
                                    <span class="me-2">🇬🇧</span>{{ t('english') }}
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" :class="{ active: locale === 'kh' }" type="button" @click="changeLocale('kh')">
                                    <span class="me-2">🇰🇭</span>{{ t('khmer') }}
                                </button>
                            </li>
                        </ul>
                    </li>

                    <!-- User dropdown -->
                    <li class="nav-item dropdown dropdown-large">
                        <a
                            class="nav-link dropdown-toggle dropdown-toggle-nocaret has-caret"
                            href="#"
                            data-bs-toggle="dropdown"
                            :aria-label="user?.name || 'User menu'"
                        >
                            <div class="user-setting d-flex align-items-center gap-2">
                                <span class="user-img">{{ userInitials }}</span>
                                <span class="user-name d-none d-sm-block">{{ user?.name || 'Guest' }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-user-card">
                                    <span class="user-img">{{ userInitials }}</span>
                                    <div>
                                        <h6 class="dropdown-user-name">{{ user?.name }}</h6>
                                        <small>{{ user?.role?.name || t('user') }}</small>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <Link class="dropdown-item" :href="route('admin.profile')">
                                    <i class="bi bi-person me-2"></i>{{ t('profile') }}
                                </Link>
                            </li>
                            <li>
                                <Link class="dropdown-item" :href="route('admin.settings.index')">
                                    <i class="bi bi-gear me-2"></i>{{ t('settings') }}
                                </Link>
                            </li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <button class="dropdown-item" type="button" @click="logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ t('logout') }}
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>
