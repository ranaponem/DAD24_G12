<script setup>
  import { ref  } from 'vue';

  const number = ref(0);

  const increase = () => {
    number.value += 10;
  };

  const decrease = () => {
    if (number.value > 0) 
        number.value -= 10;     
  };

  const validateInput = () => {
    if (!number.value || number.value < 0)
        number.value = 0;
  };
</script>
<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <div class="flex items-center space-x-4 bg-white shadow-lg p-6 rounded-lg">
      <button @click="decrease" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 focus:outline-none">
        -10
    </button>
    <input
      type="number"
      v-model.number="number"
      @input="validateInput"
      class="w-20 text-center text-2xl font-bold
      bg-gray-200 rounded-lg shadow-inner
      focus:outline-none focus:ring-2
      focus:ring-blue-400"
    />
    <button @click="increase" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 focus:outline-none">
      +10
    </button>
    </div>
  </div>
</template>
<style>
/* Remove arrows in number inputs for cross-browser compatibility */
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
        margin: 0;
          
}
input[type='number'] {
    -moz-appearance: textfield; /* Firefox */
      
}
</style>
