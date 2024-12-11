<script setup>
const model = defineModel()

const props = defineProps({
    label: { type: String, default: '' },
    readonly: { type: Boolean, default: false },
    inputType: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    errorMessage: { type: String, default: '' },
})

</script>

<template>
    <div class="flex flex-col gap-2 self-stretch">
        <label v-if="props.label" class="text-sm text-gray-500 dark:text-gray-300">{{ props.label }}</label>
        <input :type="props.inputType" class="px-2 py-1 text-md rounded-lg border-secondary-dark dark:border-secondary-light border-2" :placeholder="props.placeholder"
            v-model="model" :readonly="props.readonly"/>
        <div v-show="errorMessage" class="text-sm text-red-600 ps-5">{{ errorMessage }}</div>
    </div>
</template>