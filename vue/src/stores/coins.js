import { computed } from 'vue'
import { defineStore } from 'pinia'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

export const useCoinsStore = defineStore('coins', () => {
  const storeError = useErrorStore()
  const storeAuth = useAuthStore()

  const myBalance = computed(()=>{
    return storeAuth.balance ?? 0
  })

  const buyCoins = async (quantity, reference, paymentType) => {
    try{
      const response = await axios.post('transactions', {brain_coins: quantity , type: 'P', payment_type: paymentType, payment_ref: reference})
    }
    catch{
      storeError.setErrorMessages(
        e.response.data.message,
        e.response.data.errors,
        e.response.status,
        'Authentication Error!'
      )
      return false
    }
  }

  return {
    buyCoins,
    myBalance
  }
})
