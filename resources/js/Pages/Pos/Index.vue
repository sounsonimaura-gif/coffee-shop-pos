<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TomSelectField from '@/Components/TomSelectField.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    menuItems: { type: Array, default: () => [] },
    tables: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    paymentMethods: { type: Array, default: () => [] },
});

const { t } = useI18n();

const activeCategory = ref(null);

const visibleItems = computed(() => {
    if (!activeCategory.value) return props.menuItems;
    return props.menuItems.filter((i) => i.category_id === activeCategory.value);
});

const cart = ref([]);

function addItem(item) {
    const existing = cart.value.find((c) => c.menu_item_id === item.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.value.push({
            menu_item_id: item.id,
            name: item.name,
            quantity: 1,
            unit_price: Number(item.sale_price || 0),
            note: '',
        });
    }
}

function changeQty(line, delta) {
    line.quantity = Math.max(0, line.quantity + delta);
    if (line.quantity === 0) {
        cart.value = cart.value.filter((c) => c !== line);
    }
}

function removeItem(line) {
    cart.value = cart.value.filter((c) => c !== line);
}

const subtotal = computed(() => cart.value.reduce((s, l) => s + l.quantity * l.unit_price, 0));

const orderForm = useForm({
    order_type: 'takeaway',
    customer_id: null,
    table_id: null,
    discount_amount: 0,
    tax_amount: 0,
    note: '',
    items: [],
    payments: [{ payment_method_id: props.paymentMethods?.[0]?.id ?? null, amount: 0, reference_no: '' }],
});

const totals = computed(() => {
    const grand = Math.max(0, subtotal.value - Number(orderForm.discount_amount || 0) + Number(orderForm.tax_amount || 0));
    return {
        subtotal: subtotal.value,
        grand,
    };
});

function pay() {
    orderForm.items = cart.value;
    orderForm.payments = orderForm.payments.map((p) => ({
        ...p,
        amount: Number(p.amount || totals.value.grand),
    }));
    orderForm.post(route('admin.orders.store'), {
        preserveScroll: true,
        onSuccess: () => {
            cart.value = [];
            orderForm.reset('note', 'discount_amount', 'tax_amount');
        },
    });
}
</script>

<template>
    <AdminLayout :title="t('pos')" :page-title="t('pos')">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm" :class="activeCategory ? 'btn-outline-primary' : 'btn-primary'" @click="activeCategory = null">
                                {{ t('all') }}
                            </button>
                            <button v-for="cat in categories" :key="cat.id" class="btn btn-sm" :class="activeCategory === cat.id ? 'btn-primary' : 'btn-outline-primary'" @click="activeCategory = cat.id">
                                {{ cat.name }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div v-for="item in visibleItems" :key="item.id" class="col-6 col-md-4 col-xl-3">
                                <button class="btn btn-outline-secondary w-100 text-start py-3" @click="addItem(item)">
                                    <div class="fw-semibold">{{ item.name }}</div>
                                    <small class="text-muted">{{ Number(item.sale_price).toFixed(2) }}</small>
                                </button>
                            </div>
                            <div v-if="!visibleItems.length" class="col-12 text-center text-muted py-4">
                                {{ t('no_items') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ t('current_order') }}</h5>
                    </div>
                    <div class="card-body p-2">
                        <FormField :label="t('order_type')">
                            <TomSelectField
                                v-model="orderForm.order_type"
                                :options="[
                                    { value: 'dine_in', text: t('dine_in') },
                                    { value: 'takeaway', text: t('takeaway') },
                                    { value: 'delivery', text: t('delivery') },
                                ]"
                                value-field="value"
                                label-field="text"
                            />
                        </FormField>
                        <FormField :label="t('customer')">
                            <TomSelectField
                                v-model="orderForm.customer_id"
                                :options="customers"
                                value-field="id"
                                label-field="name"
                                clearable
                            />
                        </FormField>
                        <FormField v-if="orderForm.order_type === 'dine_in'" :label="t('table')">
                            <TomSelectField
                                v-model="orderForm.table_id"
                                :options="tables.map((t) => ({ id: t.id, name: t.table_no + (t.name ? ' — ' + t.name : '') }))"
                                value-field="id"
                                label-field="name"
                                clearable
                            />
                        </FormField>

                        <hr />

                        <ul class="list-group list-group-flush mb-2">
                            <li v-for="(line, i) in cart" :key="i" class="list-group-item d-flex align-items-center px-0">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ line.name }}</div>
                                    <small class="text-muted">{{ Number(line.unit_price).toFixed(2) }} × {{ line.quantity }} = {{ (line.unit_price * line.quantity).toFixed(2) }}</small>
                                </div>
                                <div class="btn-group btn-group-sm me-2">
                                    <button class="btn btn-outline-secondary" @click="changeQty(line, -1)"><i class="bi bi-dash"></i></button>
                                    <button class="btn btn-outline-secondary" @click="changeQty(line, 1)"><i class="bi bi-plus"></i></button>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" @click="removeItem(line)"><i class="bi bi-x"></i></button>
                            </li>
                            <li v-if="!cart.length" class="list-group-item text-center text-muted px-0">
                                {{ t('no_items') }}
                            </li>
                        </ul>

                        <div class="row g-2">
                            <div class="col-6">
                                <FormField :label="t('discount')">
                                    <input v-model.number="orderForm.discount_amount" type="number" step="0.01" class="form-control form-control-sm" />
                                </FormField>
                            </div>
                            <div class="col-6">
                                <FormField :label="t('tax')">
                                    <input v-model.number="orderForm.tax_amount" type="number" step="0.01" class="form-control form-control-sm" />
                                </FormField>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-2">
                            <span>{{ t('subtotal') }}</span>
                            <span>{{ subtotal.toFixed(2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>{{ t('total') }}</span>
                            <span class="text-primary">{{ totals.grand.toFixed(2) }}</span>
                        </div>

                        <FormField :label="t('payment_method')">
                            <TomSelectField
                                v-model="orderForm.payments[0].payment_method_id"
                                :options="paymentMethods"
                                value-field="id"
                                label-field="name"
                            />
                        </FormField>
                        <FormField :label="t('paid_amount')">
                            <input v-model.number="orderForm.payments[0].amount" type="number" step="0.01" class="form-control" :placeholder="totals.grand.toFixed(2)" />
                        </FormField>

                        <button class="btn btn-success w-100" :disabled="!cart.length || orderForm.processing" @click="pay">
                            <i class="bi bi-credit-card me-1"></i>{{ t('charge') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
