<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
import flatpickr from 'flatpickr';

const props = defineProps({
    modelValue: { type: [String, Number, Date, null], default: null },
    enableTime: { type: Boolean, default: false },
    timeOnly: { type: Boolean, default: false },
    dateFormat: { type: String, default: 'Y-m-d' }, // accepts override
    altFormat: { type: String, default: 'Y-m-d' },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    minDate: { type: [String, Date, null], default: null },
    maxDate: { type: [String, Date, null], default: null },
});

const emit = defineEmits(['update:modelValue']);

const inputEl = ref(null);
let fp = null;

function init() {
    if (!inputEl.value) return;
    if (fp) {
        fp.destroy();
        fp = null;
    }

    let format = props.dateFormat;
    if (props.timeOnly) format = 'H:i';
    else if (props.enableTime) format = 'Y-m-d H:i';

    fp = flatpickr(inputEl.value, {
        enableTime: props.enableTime || props.timeOnly,
        noCalendar: props.timeOnly,
        dateFormat: format,
        altFormat: props.altFormat,
        time_24hr: true,
        minDate: props.minDate,
        maxDate: props.maxDate,
        allowInput: true,
        onChange: (_dates, dateStr) => {
            emit('update:modelValue', dateStr || null);
        },
    });

    if (props.modelValue) fp.setDate(props.modelValue, false);
}

onMounted(() => init());
onBeforeUnmount(() => fp && fp.destroy());

watch(
    () => props.modelValue,
    (val) => {
        if (!fp) return;
        if (val) fp.setDate(val, false);
        else fp.clear();
    }
);
</script>

<template>
    <input
        ref="inputEl"
        type="text"
        class="form-control"
        :placeholder="placeholder"
        :disabled="disabled"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value || null)"
    />
</template>
