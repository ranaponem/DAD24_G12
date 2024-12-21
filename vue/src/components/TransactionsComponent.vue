<script setup>
import { onMounted, ref, watch } from 'vue';
import { useTransactionsStore } from '@/stores/transactions';
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const transactionsStore = useTransactionsStore();
const pageNum = ref(1);
const userNickname = ref('');
const transactionType = ref('ALL');
const totalPages = ref(1);

const isAdmin = authStore.userType == 'A';

const fetchTransactions = async () => {
        try {
                if (isAdmin) {
                        await transactionsStore.getTransactions(pageNum.value, userNickname.value, transactionType.value);
                        console.log(transactionsStore.transactions)
                    } else {
                        await transactionsStore.getMyTransactions(pageNum.value, transactionType.value);
                }

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
                ALL: 'All',
                I: 'Internal',
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

const clearUserNickname = () => {
        userNickname.value = '';
        fetchTransactions();
};

watch([pageNum, userNickname, transactionType], fetchTransactions);

onMounted(() => {
        fetchTransactions();
});
</script>

<template>
    <div class="flex flex-col items-center justify-center flex-grow">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-12 mt-8 text-primary">
            {{ !isAdmin ? 'My' : getTransactionType(transactionType) }} Transactions
        </h1>
        <div class="flex flex-row items-center justify-between w-fit">
            <!-- Filter by Type -->
            <div class="bg-white p-4 mb-4 mr-4 flex items-center space-x-2 rounded-3xl border-2 border-primary justify-center">
                <label for="transactionType" class="text-lg font-bold text-gray-700 mr-2">Filter by Type:</label>
                <select
                    id="transactionType"
                    v-model="transactionType"
                    class="p-2 border rounded-lg bg-white shadow-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary"
                >
                    <option value="ALL">ALL</option>
                    <option value="I">Internal</option>
                    <option value="P">Payment</option>
                    <option value="B">Bonus</option>
                </select>
            </div>

            <!-- Search by User Nickname (Admin Only) -->
            <div v-if="isAdmin" class="bg-white p-4 mb-4 flex items-center space-x-2 rounded-3xl border-2 border-primary justify-center">
                <label for="userNickname" class="text-lg font-bold text-gray-700 mr-2">User Nickname:</label>
                <input
                    type="text"
                    id="userNickname"
                    v-model="userNickname"
                    @input="handleUserIdInput"
                    class="ml-2 py-1 text-center text-xl border border-gray-300 rounded"
                    style="width: 150px;"
                />
                <button
                    class="ml-2 px-4 py-1 bg-red-500 text-white font-bold rounded hover:bg-red-600"
                    @click="clearUserNickname"
                >
                    Clear
                </button>
            </div>
        </div>

        <!-- Scoreboard Table -->
        <div class="flex items-center justify-center w-full mb-8">
            <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-white">
                        <th v-if="isAdmin" class="bg-primary px-4 py-2 text-xl text-stone-900" style="min-width: 150px;">User</th>
                        <th 
                            v-for="(header, index) in ['Type', 'Brain Coins', 'DateTime', 'Payment Ref', 'Payment Type', 'Euros']" 
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
                        <td v-if="isAdmin" class="px-4 py-2">{{ transaction.user ? transaction.user.nickname : 'Deleted user' }}</td>
                        <td class="px-4 py-2">{{ getTransactionType(transaction.type) }}</td>
                        <td class="px-4 py-2 font-bold"
                            :class="transaction.brain_coins >= 0 ? 'text-green-600' : 'text-red-600'"
                        >
                            {{ transaction.brain_coins + ' BC' }}
                        </td>
                        <td class="px-4 py-2">{{ formatDate(transaction.transaction_datetime) }}</td>
                        <td class="px-4 py-2">{{ transaction.payment_ref !== null ? transaction.payment_ref : '---' }}</td>
                        <td class="px-4 py-2">{{ transaction.payment_type !== null ? transaction.payment_type : '---' }}</td>
                        <td class="px-4 py-2">{{ transaction.euros !== null ? transaction.euros + '€' : '---' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
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
