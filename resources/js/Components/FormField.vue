<script setup>
const props = defineProps({
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
    hint: { type: String, default: '' },
    inline: { type: Boolean, default: false },
    labelClass: { type: String, default: '' },
});
</script>

<template>
    <div :class="['mb-3', inline ? 'row' : '']">
        <label v-if="label" :class="[inline ? 'col-sm-3 col-form-label' : 'form-label', labelClass]">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <div :class="[inline ? 'col-sm-9' : '']">
            <slot />
            <div v-if="hint && !error" class="form-text">{{ hint }}</div>
            <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
        </div>
    </div>
</template>
