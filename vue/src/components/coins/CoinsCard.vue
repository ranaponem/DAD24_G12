<script setup>
  import { ref } from 'vue';

  const coinsQuantity = ref(10);
  const price = ref(coinsQuantity.value / 10);

  const increase = () => {
    if (coinsQuantity.value >= 10000) return;
    coinsQuantity.value += 10;
    updatePrice();
  };

  const decrease = () => {
    if (coinsQuantity.value <= 0) return;
    coinsQuantity.value -= 10;  
    updatePrice();
  };

  const validateInput = () => {
    if (coinsQuantity.value < 0) coinsQuantity.value = 0;

    if (coinsQuantity.value > 10000) coinsQuantity.value = 10000;

    coinsQuantity.value = Math.round(coinsQuantity.value / 10) * 10; 

    updatePrice();
  };

  const updatePrice = () => {
    price.value = coinsQuantity.value / 10;
  }
</script>
<template>
  <div class="flex justify-center items-center p-4 w-2/5">
    <div class="flex flex-col items-center bg-white shadow-lg p-6 rounded-lg">
      <div class="mb-12 text-3xl font-semibold text-gray-700">
        Purchase more coins!
      </div>
      <div class="flex flex-row justify-center space-x-4">
        <button @click="decrease" class="bg-primary text-white w-10 px-4
          py-2 rounded hover:bg-primary-dark focus:outline-none">
          -
        </button>
        <input
          type="coinsQuantity"
          v-model.coinsQuantity="coinsQuantity"
          @blur="validateInput"
          class="w-20 text-center text-2xl font-bold
          bg-gray-200 rounded-lg shadow-inner
          focus:outline-none focus:ring-2
          focus:ring-blue-400"
        />
        <button @click="increase" class="bg-primary text-white w-10 px-4
            py-2 rounded hover:bg-primary-dark focus:outline-none">
          +
        </button>
      </div>
      <div class="mt-4 text-3xl font-semibold text-gray-700">
        {{ price.toFixed(2)  }}€
      </div>
    </div>
  </div>
</template>
<style>
/* Remove arrows in coinsQuantity inputs for cross-browser compatibility */
input[type='coinsQuantity']::-webkit-inner-spin-button,
input[type='coinsQuantity']::-webkit-outer-spin-button {
    -webkit-appearance: none;
        margin: 0;
          
}
input[type='coinsQuantity'] {
    -moz-appearance: textfield; /* Firefox */
      
}
</style>
