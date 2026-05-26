<script setup>
import { computed, provide, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminHeader from '@/Layouts/Partials/AdminHeader.vue';
import AdminSidebar from '@/Layouts/Partials/AdminSidebar.vue';
import FlashMessages from '@/Components/FlashMessages.vue';

const page = usePage();
const { t } = useI18n();

const props = defineProps({
    title: { type: String, default: '' },
    breadcrumbs: { type: Array, default: () => [] }, // [{ label, route?, params? }]
    pageTitle: { type: String, default: '' },
});

const finalTitle = computed(() => props.title || props.pageTitle || t('dashboard'));

// Sidebar toggle state — desktop "collapsed" and mobile "open" overlay
const sidebarCollapsed = ref(false);
const sidebarOpenMobile = ref(false);

function toggleSidebar() {
    if (window.matchMedia('(max-width: 991.98px)').matches) {
        sidebarOpenMobile.value = !sidebarOpenMobile.value;
    } else {
        sidebarCollapsed.value = !sidebarCollapsed.value;
    }
}

function closeMobileSidebar() {
    sidebarOpenMobile.value = false;
}

provide('admin-layout', {
    toggleSidebar,
    sidebarCollapsed,
    sidebarOpenMobile,
});
</script>

<template>
    <div
        class="wrapper"
        :class="{
            'sidebar-collapsed': sidebarCollapsed,
            'sidebar-open': sidebarOpenMobile,
        }"
    >
        <Head :title="finalTitle" />

        <AdminHeader />
        <AdminSidebar />

        <div
            v-if="sidebarOpenMobile"
            class="sidebar-backdrop d-lg-none"
            @click="closeMobileSidebar"
        ></div>

        <main class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">{{ finalTitle }}</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <Link :href="route('admin.dashboard')">
                                    <i class="bx bx-home-alt"></i>
                                </Link>
                            </li>
                            <li
                                v-for="(crumb, i) in breadcrumbs"
                                :key="i"
                                class="breadcrumb-item"
                                :class="{ active: i === breadcrumbs.length - 1 }"
                                :aria-current="i === breadcrumbs.length - 1 ? 'page' : null"
                            >
                                <Link v-if="crumb.route && i !== breadcrumbs.length - 1" :href="route(crumb.route, crumb.params || {})">
                                    {{ crumb.label }}
                                </Link>
                                <span v-else>{{ crumb.label }}</span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <FlashMessages />

            <slot />
        </main>

    </div>
</template>

<style scoped>
.sidebar-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1035;
}
</style>
