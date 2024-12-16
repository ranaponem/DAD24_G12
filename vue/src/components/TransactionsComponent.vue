<script setup>
import { onMounted, ref, watch } from 'vue';
import { useTransactionsStore } from '@/stores/transactions';

const transactionsStore = useTransactionsStore();
const allTransactions = ref([]);
const pageNum = ref(1);
const totalPages = ref(1);
const fetchTransactions = async () => {
        try {
                await transactionsStore.getMyTransactions(pageNum.value);

                console.log(transactionsStore.transactions)
                if (transactionsStore.meta?.data?.meta?.last_page) {
                        totalPages.value = transactionsStore.meta.data.meta.last_page;
                } else {
                        console.warn("Meta data not found or is undefined");
                        totalPages.value = 1;
                }
        } catch (error) {
                console.error("Error fetching transactions:", error);
        }
};

const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
        });
};

const getTransactionType = (type) => {
        const transactionTypes = {
                I: 'Internal',
                P: 'Payment',
                B: 'Bonus'
        };
        return transactionTypes[type] || 'Unknown';
};

const getTransactionEuros = (type) => {
        const transactionEuros = {
                null: '-----',
                P: 'Payment',
                B: 'Bonus'
        };
        return transactionTypes[type] || 'Unknown';
};
let debounceTimeout = null;

const handlePageChange = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
                if (pageNum.value < 1) {
                        pageNum.value = 1;
                } else if (pageNum.value > totalPages.value) {
                        pageNum.value = totalPages.value;
                }
                fetchTransactions();
        }, 700);
};

onMounted(() => {
        fetchTransactions();
});

</script>

<template>
        <div class="flex flex-col items-center justify-center flex-grow">
                <!-- Title -->
                <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-12 mt-8 text-primary">
                        My Transactions
                </h1>
                <!-- Scoreboard Table -->
                <div class="flex items-center justify-center w-full mb-8">
                        <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                                <thead>
                                        <tr class="bg-white">
                                                <th 
                                                        v-for="(header, index) in ['Type','Brain Coins', 'DateTime', 'Payment Ref', 'Payment Type','Euros' ]" 
                                                        :key="index" 
                                                        class="bg-primary px-4 py-2 text-xl text-stone-900"
                                                        :style="{ minWidth: '150px' }"
                                                >
                                                        {{ header }}
                                                </th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr 
                                                v-for="(transaction, index) in transactionsStore.transactions" 
                                                :key="transaction.id" 
                                                :class="index % 2 === 0 ? 'bg-white' : 'bg-stone-200'"
                                                class="transition-colors duration-200 text-black"
                                        >
                                                <td class="px-4 py-2">{{ getTransactionType(transaction.type)}}</td>
                                                <td class="px-4 py-2 font-bold"
                                                        :class="transaction.brain_coins >= 0 ? 'text-green-600' : 'text-red-600'"
                                                >{{ transaction.brain_coins +'c' }}</td>
                                                <td class="px-4 py-2">{{ formatDate(transaction.transaction_datetime) }}</td>
                                                <td class="px-4 py-2">{{ transaction.payment_ref !== null ? transaction.payment_ref : '---' }}</td>
                                                <td class="px-4 py-2">{{ transaction.payment_type !== null ? transaction.payment_type : '---' }}</td>
                                                <td class="px-4 py-2">{{ transaction.euros !== null ? transaction.euros +'€' : '---' }}</td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
                <div class="flex items-center justify-center">
                        <button
                                class="px-4 py-2 text-white rounded-l-lg"
                                :class="pageNum === 1 ? 'bg-primary-dark' : 'bg-primary'"
                                :disabled="pageNum === 1"
                                @click="pageNum > 1 && (pageNum--, fetchTransactions())"
                        >
                                Previous
                        </button>
                        <input
                        type="text"
                        v-model.number="pageNum"
                        @input="handlePageChange"
                        class="ml-2 py-1 text-center text-xl border border-gray-300 rounded"
                        :min="1"
                        :max="totalPages"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        :maxlength="totalPages.toString().length" 
                        style="width: 50px; text-align: center;" 
                />
                        <span class="px-2 py-2 text-xl">of {{ totalPages }}</span>
                        <button
                                class="px-4 py-2 text-white rounded-r-lg"
                                :class="pageNum === totalPages ? 'bg-primary-dark' : 'bg-primary'"
                                :disabled="pageNum === totalPages"
                                @click="pageNum < totalPages && (pageNum++, fetchTransactions())"
                        >
                                Next
                        </button>
                </div>
        </div>
</template>

