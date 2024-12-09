<script setup>
import { onMounted, ref, watch } from 'vue';
import { useGamesStore } from '@/stores/games';

const gamesStore = useGamesStore();
const allGames = ref([]);
const selectedType = ref('turns'); // Declare selectedType as reactive with ref

// Fetch the scoreboard based on the selected type
const fetchScoreboard = async () => {
        allGames.value = await gamesStore.getScoreboard(selectedType.value);
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

// Watch the selectedType variable and refetch the scoreboard when it changes
watch(selectedType, fetchScoreboard);

// Fetch the initial data when the component is mounted
onMounted(() => {
        fetchScoreboard();
});
</script>

<template>
        <div class="flex flex-col items-center justify-center flex-grow">
                <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 mt-8 text-primary">
                        Scoreboard
                </h1>

                <!-- Radio buttons for TURNS and TIME -->
                <div class="bg-white p-4 mb-4 flex items-center space-x-4 rounded-3xl border-2 border-primary">
                        <label class="flex items-center space-x-2">
                                <input 
                                type="radio" 
                                value="turns" 
                                v-model="selectedType" 
                                class="form-radio text-primary" 
                        />
                                <span class="text-lg text-gray-800">TURNS</span>
                        </label>
                        <label class="flex items-center space-x-2">
                                <input 
                                type="radio" 
                                value="time" 
                                v-model="selectedType" 
                                class="form-radio text-primary" 
                        />
                                <span class="text-lg text-gray-800">TIME</span>
                        </label>
                </div>

                <div class="flex items-center justify-center flex-grow w-full">
                        <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                                <thead>
                                        <tr class="bg-white">
                                                <th 
                                                        v-for="(header, index) in ['User', 'Board Type', 'Game Type', 'Turns Taken', 'Time Spent', 'Finished At']" 
                                                        :key="index" 
                                                        class=" bg-primary px-4 py-2 text-xl text-stone-900">
                                                        {{ header }}
                                                </th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr 
                                                v-for="(game, index) in allGames" 
                                                :key="game.id" 
                                                :class="index % 2 === 0 ? 'bg-white' : 'bg-stone-200'"
                                                class="transition-colors duration-200 text-black">
                                                <td class="px-4 py-2">{{ game.won_by ? game.won_by.nickname : game.created_by.nickname }}</td>
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
