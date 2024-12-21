<script setup>
import { useCoinsStore } from '@/stores/coins'
import { useAuthStore } from '@/stores/auth'
import { ref, onMounted, computed } from 'vue';
import { useErrorStore } from '@/stores/error';

  const storeError = useErrorStore()

  const coinsQuantity = ref(0);
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
  const storeAuth = useAuthStore()

  const userBalance = ref(0) 

  const fetchUserBalance = async () => {
    const balance = await storeAuth.updateUserBalance() // Await the resolved balance
    if (balance !== false) {
      userBalance.value = balance
    }
  }

  
  onMounted(() => {
    fetchUserBalance()
  })

  const buyCoinsButton = async () => {
    await storeCoins.buyCoins({brain_coins: coinsQuantity.value,
                        type: 'P',
                        payment_type: paymentMethod.value,
                        payment_ref: reference.value
                      })
    fetchUserBalance()
  }

</script>
<template>
  <div class="w-full lg:w-3/5 h-full border-secondary-dark dark:border-secondary-light
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
          Balance: {{ userBalance }} BC
        </div>
        <div class="mb-8 text-xl font-semibold text-gray-700">
          Purchase more coins!
        </div>
        <div class="flex flex-row justify-center space-x-4">
          <button @click="decrease" 
            class="text-lg font-semibold rounded-2xl bg-cyan-600 hover:bg-cyan-800 border-2 border-secondary-dark px-6 py-2">
            -
          </button>
          <input
            type="number"
            v-model="coinsQuantity"
            @blur="validateInput"
            class="w-20 text-center text-2xl font-bold
            bg-gray-200 rounded-lg shadow-inner
            focus:outline-none focus:ring-2
            focus:ring-blue-400 border-secondary-dark border-2"
            />
          <button @click="increase" 
            class="text-lg font-semibold rounded-2xl bg-cyan-600 hover:bg-cyan-800 border-2 border-secondary-dark px-6 py-2">
            +
          </button>
        </div>
        <div v-if="coinsQuantity > 0" class="flex flex-col mt-6 gap-2 self-stretch">
          <label class="text-sm text-gray-500 dark:text-gray-300">Payment
            Type</label>
          <select id="payment-method" v-model="paymentMethod"
                                      class="w-full px-2 py-1 text-md rounded-lg border-secondary-dark
                                      dark:border-secondary-light border-2">
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

        <div v-if="coinsQuantity > 0" class="flex flex-col gap-2 self-stretch
        mt-4">
        <label class="text-sm text-gray-500 dark:text-gray-300">Reference</label>
        <input type="text" class="px-2 py-1 text-md rounded-lg border-secondary-dark dark:border-secondary-light border-2" 
                           placeholder="Enter reference here" v-model="reference" />
        <div class="text-sm text-red-600 ps-5">{{ storeError.fieldMessage('payment_ref') }}</div>
        </div>

        <button @click.prevent="buyCoinsButton" v-if="coinsQuantity > 0"
                                                class="mt-5 text-lg font-semibold rounded-2xl bg-cyan-600 hover:bg-cyan-800 border-2 border-secondary-dark px-6 py-2">
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
