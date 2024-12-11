import { ref } from 'vue';
import { defineStore } from "pinia";
import axios from 'axios';
import { useErrorStore } from "./error";

export const useGamesStore = defineStore('games', () => {

        const storeError = useErrorStore()

        const myGames = ref([]) 
        const allGames = ref([]) 
        const meta = ref({})

        const getHistory = async(pageNum) => {
                const params = new URLSearchParams([['page', pageNum]])
                try{
                        const responseMyGames = await axios.get('/games/my', { params })
                        myGames.value = responseMyGames.data.data;
                        meta.value = responseMyGames;
                        return myGames.value;
                }
                catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching History'
                        );
                        return [];
                }
        };

        const getScoreboard = async(sorting, board, scope) => {
                let params;

                if(scope == "GLOBAL"){
                        if(board == "ALL"){
                                params = new URLSearchParams([['score_type', sorting]])
                        }else{

                                params = new URLSearchParams([['score_type', sorting],['board', board]])
                        }
                        try{
                                const responseAllGames = await axios.get('/games', { params })
                                allGames.value = responseAllGames.data.data;
                                return allGames.value;
                        }
                        catch (e) {
                                storeError.setErrorMessages(
                                        e.response.data.message,
                                        e.response.data.errors,
                                        e.response.status,
                                        'Error fetching Scoreboard'
                                );
                                return [];
                        }
                }
                else if(scope == "PERSONAL"){
                        if(board == "ALL"){
                                params = new URLSearchParams([['score_type', sorting]])
                        }else{

                                params = new URLSearchParams([['score_type', sorting],['board', board]])
                        }
                        try{
                                const responseAllGames = await axios.get('/games/my', { params })
                                allGames.value = responseAllGames.data.data;
                                return allGames.value;
                        }
                        catch (e) {
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
