import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'

import avatarNoneAssetURL from '@/assets/avatar-none.png'

export const useAuthStore = defineStore('auth', () => {
  const socket = inject('socket') 
  
  const storeError = useErrorStore()

  const user = ref(null)
  const token = ref('')

  const userName = computed(() => {
    return user.value ? user.value.name : ''
  })

  const userFirstLastName = computed(() => {
    const names = userName.value.trim().split(' ')
    const firstName = names[0] ?? ''
    const lastName = names.length > 1 ? names[names.length - 1] : ''
    return (firstName + ' ' + lastName).trim()
  })

  const userEmail = computed(() => {
    return user.value ? user.value.email : ''
  })

  const userType = computed(() => {
    return user.value ? user.value.type : ''
  })

  const userPhotoUrl = computed(() => {
    const photoFile = user.value ? (user.value.photo_filename ?? '') : ''
    if (photoFile) {
      return axios.defaults.baseURL.replaceAll('/api', "/" + photoFile)
    }
    return avatarNoneAssetURL
  })

  const userBalance = async () => {
    try{
      const responseCoins = await axios.get('users/mybalance')
      const balance = ref(responseCoins.data.brain_coins_balance)
      return balance.value
    }
    catch(e){
      storeError.setErrorMessages(
        e.response.data.message,
        e.response.data.errors,
        e.response.status,
        'Balance Error!'
      )
      return false
    }
  }

  // This function is "private" - not exported by the store
  const clearUser = () => {
    resetIntervalToRefreshToken()
    if (user.value)
      socket.emit('logout', user.value)
    user.value = null
    axios.defaults.headers.common.Authorization = ''
  }

  const login = async (credentials) => {
    storeError.resetMessages()
    try {
      const responseLogin = await axios.post('auth/login', credentials)
      token.value = responseLogin.data.token
      axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
      const responseUser = await axios.get('users/me')
      user.value = responseUser.data.data
      repeatRefreshToken()
      return user.value
    } catch (e) {
      clearUser()
      storeError.setErrorMessages(
        e.response.data.message,
        e.response.data.errors,
        e.response.status,
        'Authentication Error!'
      )
      return false
    }
  }

  const logout = async () => {
    storeError.resetMessages()
    try {
      await axios.post('auth/logout')
      clearUser()
      return true
    } catch (e) {
      clearUser()
      storeError.setErrorMessages(
        e.response.data.message,
        [],
        e.response.status,
        'Authentication Error!'
      )
      return false
    }
  }

  const deleteAccount = async (credentials) => {
    try {
        await axios.delete('users/me', { password: credentials })
        clearUser()
        return true
    } catch (e) {
        return e
    }
  }

  let intervalToRefreshToken = null

  const resetIntervalToRefreshToken = () => {
    if (intervalToRefreshToken) {
      clearInterval(intervalToRefreshToken)
    }
    intervalToRefreshToken = null
  }

  const repeatRefreshToken = () => {
    if (intervalToRefreshToken) {
      clearInterval(intervalToRefreshToken)
    }
    intervalToRefreshToken = setInterval(
      async () => {
        try {
          const response = await axios.post('auth/refreshtoken')
          token.value = response.data.token
          axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
          return true
        } catch (e) {
          clearUser()
          storeError.setErrorMessages(
            e.response.data.message,
            e.response.data.errors,
            e.response.status,
            'Authentication Error!'
          )
          return false
        }
      },
      1000 * 60 * 110
    )
    return intervalToRefreshToken
  }

  return {
    user,
    userName,
    userFirstLastName,
    userEmail,
    userType,
    userPhotoUrl,
    userBalance,
    login,
    logout,
    deleteAccount
  }
})
