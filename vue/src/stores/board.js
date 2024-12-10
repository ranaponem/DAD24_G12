import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'

export const useBoardStore = defineStore('boards', () => {
    const storeError = useErrorStore()
    const boards = ref([])

    const fetchBoards = async () => {
        storeError.resetMessages()
        const response = await axios.get('boards')
        boards.value = response.data.data
    }

    const createSingleplayerGame = async (board) => {
        try {
            const response = await axios.post('/games', {
                type: 'S',
                board_id: board.id,
            })
            console.log('Game created:', response.data)
            alert('Game created successfully!')
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
            console.log('Game created:', response.data)
            alert('Game created successfully!')
        } catch (error) {
            console.error('Error creating game:', error.response?.data || error.message)
            alert('Failed to create the game.')
        }
    }

    return{
        boards,fetchBoards, createSingleplayerGame, createMultiplayerGame
    }
})