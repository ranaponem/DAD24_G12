<script setup>
import Card from './Card.vue'


const props = defineProps({
  board: {
    type: Array,
    required: true
  },
  size: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['play'])

const chooseCardOnBoard = (index) => {
  emit('play', index)
}

</script>

<template>
  <div class="flex w-full h-full max-w-xl justify-center items-center max-h-fit bg-gray-400 rounded-xl">
    <div
      :class="`w-full h-full gap-2 p-2 inline-grid justify-center items-center ${ size == 16 ? 'grid-cols-4' : size == 36 ? 'grid-cols-6' : 'grid-cols-3' }`">
      <Card v-for="(card, index) in board" :key="index" :card-number="card.value" :index="index"
        :is-flipped="card.isFlipped" :is-matched="card.isMatched" @flip="chooseCardOnBoard" />
    </div>
  </div>
</template>
