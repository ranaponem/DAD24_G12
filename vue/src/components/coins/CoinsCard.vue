<script setup>
  import { useCoinsStore } from '@/stores/coins'
  import { ref } from 'vue';

  const coinsQuantity = ref(10);
  const price = ref(coinsQuantity.value / 10);
  const reference = ref(null);
  const paymentMethod = ref("MBWAY");

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

  const storeCoins = useCoinsStore()
</script>
<template>
  <div class="w-full h-full border-secondary-dark dark:border-secondary-light
  border-2 bg-gray-50 dark:bg-gray-800 shadow-lg rounded-lg
  overflow-hidden">
    <div
      class="flex flex-col items-center font-semibold
      text-gray-50 text-2xl bg-primary py-6 px-32
      space-y-3">
      My coins
    </div>
    <div class="border-2
    border-gray-900
    dark:border-gray-100" />
        <div class="flex flex-col items-center bg-white shadow-lg p-6 rounded-lg">
          <div class="text-3xl mb-12 font-semibold text-gray-700">
            Balance: {{ storeCoins.myBalance }} BC
          </div>
          <div class="mb-8 text-xl font-semibold text-gray-700">
            Purchase more coins!
          </div>
          <div class="flex flex-row justify-center space-x-4">
            <button @click="decrease" class="bg-primary text-white w-10 px-4
            py-2 rounded hover:bg-primary-dark focus:outline-none">
              -
            </button>
            <input
              type="number"
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
          <div class="mt-6 w-full">
            <label for="payment-method" class="block mb-2 text-gray-600
            font-medium">Select Payment Method:</label>
            <select id="payment-method" v-model="paymentMethod"
                                        class="w-full p-2 border rounded-lg shadow-sm
                                        focus:outline-none focus:ring-2 focus:ring-blue-400">
              <option value="MBWAY" selected>MBWAY</option>
              <option value="PAYPAL">PAYPAL</option>
              <option
                value="IBAN">IBAN</option>
              <option
                value="MB">MB</option>
              <option
                value="VISA">VISA</option>
            </select>
          </div>

          <!-- Textbox for reference -->
          <div class="mt-4 w-full">
            <label for="reference" class="block mb-2 text-gray-600
            font-medium">Reference:</label>
            <input
              type="number"
              id="reference"
              v-model="reference"
              placeholder="Enter reference here"
              class="w-full p-2
              border rounded-lg
              shadow-sm
              focus:outline-none
              focus:ring-2
              focus:ring-blue-400"
              />
          </div>
          <button v-if="coinsQuantity > 0" class="mt-8 bg-primary-light hover:bg-primary focus:outline-none
          px-4 py-2 rounded text-xl font-semibold text-white">
          Buy for {{ price.toFixed(2)  }}€
          </button>
        </div>
    </div>
</template>
<style>
/* Remove arrows in coinsQuantity inputs for cross-browser compatibility */
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;

}
input[type='number'] {
  -moz-appearance: textfield; /* Firefox */

}
</style>
