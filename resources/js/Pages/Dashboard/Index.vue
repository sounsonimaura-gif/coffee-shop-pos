<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const page = usePage();

const cards = computed(() => [
    { label: t('total_sales_today'), value: props.stats.sales_today ?? '0.00', icon: 'bi-cash-coin', bg: 'bg-primary' },
    { label: t('orders_today'), value: props.stats.orders_today ?? 0, icon: 'bi-receipt', bg: 'bg-success' },
    { label: t('open_shifts'), value: props.stats.open_shifts ?? 0, icon: 'bi-clock-history', bg: 'bg-warning' },
    { label: t('low_stock_items'), value: props.stats.low_stock_items ?? 0, icon: 'bi-exclamation-triangle', bg: 'bg-danger' },
]);
</script>

<template>
    <AdminLayout :title="t('dashboard')" :page-title="t('dashboard')">
        <div class="row g-3">
            <div v-for="(card, i) in cards" :key="i" class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-3 p-3 me-3 text-white" :class="card.bg">
                            <i class="bi" :class="card.icon" style="font-size: 1.5rem"></i>
                        </div>
                        <div>
                            <div class="small text-muted">{{ card.label }}</div>
                            <h3 class="mb-0">{{ card.value }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">{{ t('welcome') }}, {{ page.props.auth?.user?.name }}</h5>
                <p class="text-muted mb-0">{{ t('dashboard_welcome_text') }}</p>
            </div>
        </div>
    </AdminLayout>
</template>
