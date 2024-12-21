<script setup>
const model = defineModel()

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    readonly: { type: Boolean, default: false },
    inputType: { type: String, default: 'text' },
    placeholder: { type: String, default: null },
    errorMessage: { type: String, default: null },
    asErrors: { type: Number, default: -1 },
    autofocus: { type: Boolean, default: false },
})

</script>

<template>
    <div class="flex flex-col space-y-2 self-stretch">
        <label v-if="props.label" class="text-sm text-gray-500 dark:text-gray-300">{{ props.label }}</label>
        <input :type="props.inputType"
        :class="`p-2 text-md rounded-md border-2 ${
            !readonly && (props.asErrors > -1) ?
                errorMessage || (props.asErrors > 0) ?
                    'border-red-600 dark:border-red-800 focus:ring-red-400 dark:focus:ring-red-600' 
                    : 'border-lime-500 dark:border-lime-700 focus:ring-lime-300 dark:focus:ring-lime-500' 
                : 'border-secondary-light dark:border-secondary-light focus:ring-purple-500 dark:focus:ring-purple-700'
            } focus:outline-none focus:border-transparent focus:ring-1`"
            :placeholder="props.placeholder" v-model="model" :id="id" :readonly="props.readonly" :autofocus="autofocus"/>
        <div v-show="errorMessage" class="text-sm text-red-600 ps-5">{{ errorMessage }}</div>
    </div>
</template>