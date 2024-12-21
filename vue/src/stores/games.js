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
        let params = new URLSearchParams(); 
        params.append('type', 'A');
        params.append('page', pageNum); 

        if (board !== "ALL") {
            params.append('board', board);
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

    const getScoreboard = async (sorting, board, scope, type) => {
        let params = new URLSearchParams(); 

        if(type !== "M"){
            params.append('score_type', sorting); 
            params.append('type', type); 
            params.append('status', "E");

            if (board !== "ALL") {
                params.append('board', board); 
            }
        }

        try {
            let responseAllGames;

            if (type === "S"){
                if (scope === "GLOBAL") {
                    responseAllGames = await axios.get('/gamesended', { params });
                }else if(scope === "PERSONAL"){
                    responseAllGames = await axios.get('/games/my', { params });
                }

                allGames.value = responseAllGames.data.data;
            }else if(type === "M"){
                responseAllGames = await axios.get('/topfivemultiplayer', { params });

                allGames.value = responseAllGames.data;
            }

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
    };

    return { myGames, meta, getHistory, getScoreboard };
})
