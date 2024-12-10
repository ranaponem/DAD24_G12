import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'
import { useBoardStore } from './board'

const storeBoard = useBoardStore()

export const useGameStore = defineStore('games', () => {
    const storeError = useErrorStore()
    const memoryGames = ref([])
    const game = ref([])

    const fetchGames = async () => {
        storeError.resetMessages()
        const response = await axios.get('games')
        memoryGames.value = response.data.data
    }

    const getGame = async (game) =>{
        storeError.resetMessages()
        const response = await axios.get(`games/${game.id}`)
        game.value = response.data.data
    }

    const createSingleplayerGame = async (board) => {
        try {
            const response = await axios.post('/games', {
                type: 'S',
                board_id: board.id,
            })
            game.value = response.data.data
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Authentication Error!'
              )
              return false
        }
    }

    const createMultiplayerGame = async (board) => {
        try {
            const response = await axios.post('/games', {
                type: 'M',
                board_id: board.id,
            })
            game.value = response.data.data
        } catch (error) {
            console.error('Error creating game:', error.response?.data || error.message)
            alert('Failed to create the game.')
        }
    }

    return {
        memoryGames,game,fetchGames,getGame,createSingleplayerGame,createMultiplayerGame
    }
})