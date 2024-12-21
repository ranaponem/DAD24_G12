<script setup>
import { ref, watch } from 'vue';


const props = defineProps([
  'cardNumber',
  'index',
  'isFlipped',
  'isMatched']
)

const emit = defineEmits(['flip'])

const flipCard = () => {
  if (!props.isFlipped && !props.isMatched) {
    emit('flip', props.index);
  }
}

const isInvisible = ref('')
const changeMatched = () => {
  setTimeout(() => {
    isInvisible.value = 'invisible'
  }, 1000)
}

watch(
  () => props.isMatched,
  (newMatch) => {
    if(newMatch)
      changeMatched()
  })

</script>

<template>
  <div class="w-full h-full cursor-pointer flex justify-center items-center" @click="flipCard">
    <div
      class="w-full h-full max-x-36 max-h-48 relative">
      <div v-show="props.isFlipped" :class="`absolute w-full h-full ${ isInvisible }`">
        <img :src="`/cards/card_${props.cardNumber}.png`" alt="Card image" class="w-full h-full object-contain" />
      </div>
      <div v-show="!props.isFlipped" :class="`absolute w-full h-full ${ isInvisible }`">
        <img src="/cards/card_back_image.jpg" alt="Card back" class="w-full h-full object-contain" />
      </div>
    </div>
  </div>
</template>