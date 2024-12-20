import { ref } from 'vue';
import { defineStore } from "pinia";
import axios from 'axios';
import { useErrorStore } from "./error";

export const useGamesStore = defineStore('games', () => {

        const storeError = useErrorStore()

        const myGames = ref([]) 
        const allGames = ref([]) 
        const meta = ref({})

        const getHistory = async (pageNum, board) => {
                let params = new URLSearchParams(); // Declare params outside of the block

                if (board === "ALL") {
                        params.append('page', pageNum); // Add only the page parameter
                } else {
                        params.append('page', pageNum);
                        params.append('board', board); // Add both page and board parameters
                }

                try {
                        const responseMyGames = await axios.get('/games/my', { params });
                        myGames.value = responseMyGames.data.data;
                        meta.value = responseMyGames;
                        return myGames.value;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching History'
                        );
                        return [];
                }
        };

const getScoreboard = async (sorting, board, scope) => {
    let params = new URLSearchParams(); // Declare params outside

    if (scope === "GLOBAL") {
        params.append('score_type', sorting); // Always include score_type

        if (board !== "ALL") {
            params.append('board', board); // Add board filter if it's not "ALL"
        }

        try {
            const responseAllGames = await axios.get('/games', { params });
            allGames.value = responseAllGames.data.data;
            return allGames.value;
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Error fetching Scoreboard'
            );
            return [];
        }
    } else if (scope === "PERSONAL") {
        params.append('score_type', sorting); // Always include score_type

        if (board !== "ALL") {
            params.append('board', board); // Add board filter if it's not "ALL"
        }

        try {
            const responseAllGames = await axios.get('/games/my', { params });
            allGames.value = responseAllGames.data.data;
            return allGames.value;
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Error fetching Scoreboard'
            );
            return [];
        }
    }
};

        return { myGames, meta, getHistory, getScoreboard };
})
