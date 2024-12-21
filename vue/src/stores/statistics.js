import { ref } from 'vue';
import { defineStore } from "pinia";
import axios from 'axios';
import { useErrorStore } from "./error";

export const useStatisticsStore = defineStore('statistics', () => {

        const storeError = useErrorStore()

        const profit = ref([]) 

        const getDetailedProfit = async (timeRange) => {
                let params = new URLSearchParams();
                params.append('time_range', timeRange);

                try {
                        const response = await axios.get('/statistics/detailed-profit', { params });
                        return response.data;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching detailed statistics'
                        );
                        return [];
                }
        };

        const getTotalUsers = async (timeRange) => {
                let params = new URLSearchParams();
                params.append('time_range', timeRange);

                try {
                        const response = await axios.get('/statistics/total-users', { params });
                        return response.data;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching detailed statistics'
                        );
                        return [];
                }
        };

        const getTotalGames = async (timeRange) => {
                let params = new URLSearchParams();
                params.append('time_range', timeRange);

                try {
                        const response = await axios.get('/statistics/total-games', { params });
                        return response.data;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching detailed statistics'
                        );
                        return [];
                }
        };

        return { profit, getDetailedProfit, getTotalUsers, getTotalGames};

})
