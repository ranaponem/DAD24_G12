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

    return{
        boards,
        fetchBoards
    }
})