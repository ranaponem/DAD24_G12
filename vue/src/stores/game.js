import { ref } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';
import { useErrorStore } from '@/stores/error';

export const useGameStore = defineStore('games', () => {
    const storeError = useErrorStore();
    const memoryGames = ref([]);
    const game = ref([]);

    const fetchGames = async () => {
        storeError.resetMessages();
        try {
            const response = await axios.get('games');
            memoryGames.value = response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response?.data?.message || 'Failed to fetch games.',
                e.response?.data?.errors || [],
                e.response?.status || 500,
                'Error fetching games!'
            );
        }
    };

    const getGame = async (game) => {
        storeError.resetMessages();
        try {
            const response = await axios.get(`games/${game.id}`);
            game.value = response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response?.data?.message || 'Failed to fetch game details.',
                e.response?.data?.errors || [],
                e.response?.status || 500,
                'Error fetching game details!'
            );
        }
    };

    const createSingleplayerGame = async (board) => {
        try {
            const response = await axios.post('/games', {
                type: 'S',
                board_id: board.id,
            });
            game.value = response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response?.data?.message || 'Failed to create singleplayer game.',
                e.response?.data?.errors || [],
                e.response?.status || 500,
                'Authentication Error!'
            );
            return false;
        }
    };

    const createMultiplayerGame = async (board) => {
        try {
            const response = await axios.post('/games', {
                type: 'M',
                board_id: board.id,
            });
            game.value = response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response?.data?.message || 'Failed to create multiplayer game.',
                e.response?.data?.errors || [],
                e.response?.status || 500,
                'Error creating multiplayer game!'
            );
            return false;
        }
    };

    return {
        memoryGames,
        game,
        fetchGames,
        getGame,
        createSingleplayerGame,
        createMultiplayerGame,
    };
});
