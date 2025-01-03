import { defineStore } from 'pinia'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { useAuthStore } from './auth'

export const useCoinsStore = defineStore('coins', () => {
  const storeAuth = useAuthStore()
  const storeError = useErrorStore()

  const buyCoins = async (body) => {
    storeError.resetMessages()
    try{
      const response = await axios.post('transactions', body)
      storeAuth.updateUserBalance()
      return true
    }
    catch(e){
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
    buyCoins
  }
})
