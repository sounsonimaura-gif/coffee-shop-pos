<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
import TomSelect from 'tom-select';

const props = defineProps({
    modelValue: { type: [String, Number, Array, null], default: null },
    options: { type: Array, default: () => [] }, // [{ value, text }] or [{ id, name }]
    placeholder: { type: String, default: '' },
    valueField: { type: String, default: 'id' },
    labelField: { type: String, default: 'name' },
    searchField: { type: Array, default: () => ['name'] },
    multiple: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    create: { type: Boolean, default: false },
    clearable: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectEl = ref(null);
let ts = null;

function init() {
    if (!selectEl.value) return;
    if (ts) {
        ts.destroy();
        ts = null;
    }

    ts = new TomSelect(selectEl.value, {
        options: props.options.map((o) => ({
            ...o,
            value: o[props.valueField] ?? o.value,
            text: String(o[props.labelField] ?? o.text ?? ''),
        })),
        valueField: 'value',
        labelField: 'text',
        searchField: ['text', ...props.searchField.filter((f) => f !== props.labelField)],
        placeholder: props.placeholder || '',
        plugins: props.clearable ? ['clear_button', 'remove_button'] : ['remove_button'],
        maxItems: props.multiple ? null : 1,
        create: props.create,
        allowEmptyOption: true,
        onChange: (val) => {
            emit('update:modelValue', val === '' ? null : val);
            emit('change', val === '' ? null : val);
        },
    });

    setValue(props.modelValue);
    if (props.disabled) ts.disable();
}

function setValue(val) {
    if (!ts) return;
    if (Array.isArray(val)) {
        ts.setValue(val.map(String), true);
    } else if (val === null || val === undefined || val === '') {
        ts.clear(true);
    } else {
        ts.setValue(String(val), true);
    }
}

onMounted(() => init());
onBeforeUnmount(() => ts && ts.destroy());

watch(
    () => props.options,
    () => init(),
    { deep: true }
);

watch(
    () => props.modelValue,
    (v) => setValue(v)
);

watch(
    () => props.disabled,
    (v) => {
        if (!ts) return;
        v ? ts.disable() : ts.enable();
    }
);
</script>

<template>
    <select ref="selectEl" :multiple="multiple"></select>
</template>
