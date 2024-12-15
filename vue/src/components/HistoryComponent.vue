<script setup>
import { onMounted, ref } from 'vue';
import { useGamesStore } from '@/stores/games';

const gamesStore = useGamesStore();

const pageNum = ref(1);
const totalPages = ref(1);

const fetchHistory = async () => {
        try {
                await gamesStore.getHistory(pageNum.value);

                if (gamesStore.meta?.data?.meta?.last_page) {
                        totalPages.value = gamesStore.meta.data.meta.last_page;
                } else {
                        console.warn("Meta data not found or is undefined");
                        totalPages.value = 1;
                }
        } catch (error) {
                console.error("Error fetching history:", error);
        }
};

// Utility function to get board type
const getBoardType = (boardId) => {
        const boardTypes = {
                1: '3x4',
                2: '4x4',
                3: '6x6'
        };
        return boardTypes[boardId] || 'Unknown';
};

// Utility function to get game type
const getGameType = (type) => {
        const gameTypes = {
                S: 'Single Player',
                M: 'Multiplayer'
        };
        return gameTypes[type] || 'Unknown';
};

// Utility function to format date
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

let debounceTimeout = null;

const handlePageChange = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
                if (pageNum.value < 1) {
                        pageNum.value = 1;
                } else if (pageNum.value > totalPages.value) {
                        pageNum.value = totalPages.value;
                }
                fetchHistory();
        }, 700);
};

onMounted(() => {
        fetchHistory(); // Fetch data on page load
});

</script>

<template>
        <div class="flex flex-col items-center justify-center flex-grow">
                <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 mt-8 text-primary">
                        My Game History
                </h1>

                <div class="flex items-center justify-center w-full">
                        <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                                <thead>
                                        <tr class="bg-white">
                                                <th 
                                                        v-for="(header, index) in ['Board Type', 'Game Type', 'Turns Taken', 'Time Spent', 'Finished At']" 
                                                        :key="index" 
                                                        class="bg-primary px-4 py-2 text-xl text-stone-900"
                                                        :style="{ minWidth: '150px' }"
                                                >
                                                        {{ header }}
                                                </th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <!-- Check if there are no games -->
                                        <tr v-if="gamesStore.myGames.length === 0">
                                                <td 
                                                        colspan="5" 
                                                        class="px-4 py-8 text-2xl font-bold text-red-600 bg-red-100 rounded-b-lg">
                                                        You haven't played any games! 
                                                </td>
                                        </tr>
                                        <tr 
                                                v-else
                                                v-for="(game, index) in gamesStore.myGames" 
                                                :key="game.id" 
                                                :class="index % 2 === 0 ? 'bg-white' : 'bg-stone-200'"
                                                class="transition-colors duration-200 text-black">
                                                <td class="px-4 py-2">{{ getBoardType(game.board.id) }}</td>
                                                <td class="px-4 py-2">{{ getGameType(game.type) }}</td>
                                                <td class="px-4 py-2">{{ game.total_turns }}</td>
                                                <td class="px-4 py-2">{{ game.total_time }}s</td>
                                                <td class="px-4 py-2">{{ formatDate(game.ended_at) }}</td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
                <div class="flex items-center justify-center mt-6">
                        <button
                                class="px-4 py-2 text-white rounded-l-lg"
                                :class="pageNum === 1 ? 'bg-primary-dark' : 'bg-primary'"
                                :disabled="pageNum === 1"
                                @click="pageNum > 1 && (pageNum--, fetchHistory())"
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
                                @click="pageNum < totalPages && (pageNum++, fetchHistory())"
                        >
                                Next
                        </button>
                </div>
        </div>
</template>
