<script setup>
import { onMounted, ref } from 'vue';
import { useGamesStore } from '@/stores/games';

const gamesStore = useGamesStore();

const myGames = ref([]);

const fetchHistory = async () => {
        const pageNum = 1;
        myGames.value = await gamesStore.getHistory(pageNum);
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

onMounted(() => {
        fetchHistory();
});

</script>

<template>
    <div class="flex flex-col items-center justify-center flex-grow">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 mt-8 text-primary">
            My Game History
        </h1>

        <div class="flex items-center justify-center flex-grow w-full">
            <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-white">
                        <th 
                            v-for="(header, index) in ['Board Type', 'Game Type', 'Turns Taken', 'Time Spent', 'Finished At']" 
                            :key="index" 
                            class=" bg-primary px-4 py-2 text-xl text-stone-900">
                            {{ header }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Check if there are no games -->
                    <tr v-if="myGames.length === 0">
                        <td 
                            colspan="5" 
                            class="px-4 py-8 text-2xl font-bold text-red-600 bg-red-100 border border-red-500 rounded-lg">
                            You are not logged in! <br />
                            Therefore you have no games in your history!
                        </td>
                    </tr>
                    <tr 
                        v-else
                        v-for="(game, index) in myGames" 
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
    </div>
</template>
