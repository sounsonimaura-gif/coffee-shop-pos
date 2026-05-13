<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useI18n, setLocale } from '@/composables/useI18n.js';

const page = usePage();
const { t, locale } = useI18n();

const user = computed(() => page.props.auth?.user || null);
const activeBranchId = computed(() => page.props.auth?.active_branch_id);
const branches = computed(() => user.value?.branches || []);
const activeBranch = computed(() =>
    branches.value.find((b) => b.id === activeBranchId.value) || branches.value[0] || null
);

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
            <div class="mobile-toggle-icon d-xl-none">
                <i class="bi bi-list"></i>
            </div>
            <div class="top-navbar d-none d-xl-block">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <Link class="nav-link" :href="route('admin.dashboard')">{{ t('dashboard') }}</Link>
                    </li>
                    <li class="nav-item" v-if="activeBranch">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-shop me-1"></i>
                                {{ activeBranch.name }}
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
                                        <small class="text-muted ms-2">{{ b.code }}</small>
                                    </button>
                                </li>
                            </ul>
                        </div>
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
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-globe2"></i>
                            <span class="ms-1 text-uppercase">{{ locale }}</span>
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
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <div class="user-setting d-flex align-items-center gap-1">
                                <img src="/assets/backend/assets/images/avatars/avatar-1.png" class="user-img" alt="" onerror="this.style.display='none'" />
                                <div class="user-name d-none d-sm-block">{{ user?.name || 'Guest' }}</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="setting-icon">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 dropdown-user-name">{{ user?.name }}</h6>
                                            <small class="text-secondary">{{ user?.role?.name || t('user') }}</small>
                                        </div>
                                    </div>
                                </a>
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
