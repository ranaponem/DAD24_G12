<script setup>
import { readonly, ref, useTemplateRef } from 'vue';


const props = defineProps({
    label: { type: String, default: '' },
    placeholder: { type: String, required: true },
    errorMessage: { type: String, default: '' },
    readonly: { type: Boolean, default: false },
})

const emit = defineEmits(['imageUpload'])
const imagePreview = ref(null)

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (file){
    emit('imageUpload', file)
    imagePreview.value = URL.createObjectURL(file)
  }
} 

</script>

<template>
    <div class="flex flex-col gap-2 self-stretch items-center w-full h-full">
        <label v-show="props.label" class="text-sm text-gray-500 dark:text-gray-300">{{ props.label }}</label>
        <button :class="`flex relative group justify-center max-h-48 max-w-48 min-w-40 min-h-40 h-2/3 w-2/3 ${ props.readonly ? 'cursor-default' : 'cursor-pointer' }`" 
            @click.prevent="!props.readonly ? $refs.imageFile.click() : {}">
            <img :src="imagePreview ?? placeholder" :class="`object-contain max-h-48 max-w-48 min-w-40 min-h-40 h-full w-full items-center justify-center
                rounded-full ${ props.readonly ? '' : 'transition-opacity duration-300 group-hover:opacity-60'}`">
            <div v-show="!props.readonly" class="absolute inset-0 flex items-center justify-center text-black text-xl
                 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                Select image
            </div>
        </button>
        <input ref="imageFile" type="file" accept="image/" @change="onFileChange" class="invisible w-0 h-0">
        <span v-if="errorMessage" class="text-sm text-red-600 ps-5">{{ errorMessage }}</span>
    </div>
</template>