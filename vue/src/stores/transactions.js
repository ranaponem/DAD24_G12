import { ref } from 'vue';
import { defineStore } from "pinia";
import axios from 'axios';
import { useErrorStore } from "./error";

export const useTransactionsStore = defineStore('transactions', () => {

        const storeError = useErrorStore()

        const transactions = ref([]) 
        const meta = ref({})

        const getMyTransactions = async (pageNum) => {
                let params = new URLSearchParams(); 
                params.append('page', pageNum); 

                try {
                        const responseMyTransactions = await axios.get('/transactions/my', { params });
                        transactions.value = responseMyTransactions.data.data;
                        meta.value = responseMyTransactions;
                        return transactions.value;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching Transactions'
                        );
                        return [];
                }
        };

        return { transactions , meta, getMyTransactions };
})
