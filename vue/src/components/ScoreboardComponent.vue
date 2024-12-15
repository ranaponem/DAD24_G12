<script setup>
import { onMounted, ref, watch } from 'vue';
import { useGamesStore } from '@/stores/games';

const gamesStore = useGamesStore();
const allGames = ref([]);
const selectedType = ref('time'); 
const selectedBoard = ref('ALL'); 
const selectedScope = ref('GLOBAL'); // Keep track of scope
const isSpinning = ref(false);  // Track if the button is spinning

// Fetch the scoreboard based on the selected type
const fetchScoreboard = async () => {
        allGames.value = await gamesStore.getScoreboard(selectedType.value, selectedBoard.value, selectedScope.value);
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

watch([selectedType, selectedBoard, selectedScope], fetchScoreboard);

onMounted(() => {
        fetchScoreboard();
});

// Handle button click and toggle the scope
const handleButtonClick = () => {
        if (isSpinning.value) return; // Prevent click if already spinning
        isSpinning.value = true;
        selectedScope.value = selectedScope.value === 'GLOBAL' ? 'PERSONAL' : 'GLOBAL'; // Toggle scope
        setTimeout(() => {
                isSpinning.value = false;
        }, 500); // Match the duration of the animation (1 second)
};
</script>

<template>
        <div class="flex flex-col items-center justify-center flex-grow">
                <!-- Title -->
                <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-4 mt-8 text-primary">
                        Scoreboard
                </h1>
                <!-- GLOBAL/PERSONAL Switch -->
                <div class="flex items-center justify-center space-x-4 mt-2 mb-6">
                        <span 
                                :class="['text-xl font-semibold', selectedScope === 'GLOBAL' ? 'text-primary' : 'text-gray-500']">
                                GLOBAL
                        </span>
                        <button 
                                @click="handleButtonClick"
                                :class="[
                                        'w-16 h-16 p-2 bg-primary rounded-full hover:bg-primary-light focus:outline-none transition-all duration-200', 
                                        { 'animate-spin': isSpinning }
                                ]"
                                :disabled="isSpinning"
                                class="cursor-pointer disabled:opacity-50"
                        >
                                <svg class="object-cover w-full h-full" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                                <path d="M1,12A11,11,0,0,1,17.882,2.7l1.411-1.41A1,1,0,0,1,21,2V6a1,1,0,0,1-1,1H16a1,1,0,0,1-.707-1.707l1.128-1.128A8.994,8.994,0,0,0,3,12a1,1,0,0,1-2,0Zm21-1a1,1,0,0,0-1,1,9.01,9.01,0,0,1-9,9,8.9,8.9,0,0,1-4.42-1.166l1.127-1.127A1,1,0,0,0,8,17H4a1,1,0,0,0-1,1v4a1,1,0,0,0,.617.924A.987.987,0,0,0,4,23a1,1,0,0,0,.707-.293L6.118,21.3A10.891,10.891,0,0,0,12,23,11.013,11.013,0,0,0,23,12,1,1,0,0,0,22,11Z"></path>
                                        </g>
                                </svg>
                        </button>
                        <span 
                                :class="['text-xl font-semibold', selectedScope === 'PERSONAL' ? 'text-primary' : 'text-gray-500']">
                                PERSONAL
                        </span>
                </div>
                <div class="flex flex-row items-center justify-between w-fit">

                        <!-- Filter by Board Type -->
                        <div class="bg-white p-4 mb-4 mr-4 flex items-center space-x-2 rounded-3xl border-2 border-primary justify-center">
                                <label for="boardType" class="text-lg font-bold text-gray-700 mr-2">Filter by Board Type:</label>
                                <select
                                        id="boardType"
                                        v-model="selectedBoard"
                                        class="p-2 border rounded-lg bg-white shadow-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary"
                                >
                                        <option value="ALL">ALL</option>
                                        <option value="3x4">3x4</option>
                                        <option value="4x4">4x4</option>
                                        <option value="6x6">6x6</option>
                                </select>
                        </div>

                        <!-- Toggle buttons for "TIME" and "TURNS" -->
                        <div class="bg-white p-4 mb-4 flex items-center space-x-2 rounded-3xl border-2 border-primary justify-center">
                                <button
                                        :class="['px-6 py-2 rounded-full text-lg', selectedType === 'time' ? 'bg-primary text-white' : 'bg-white hover:bg-primary-light text-gray-800']"
                                        @click="selectedType = 'time'"
                                >
                                        TIME
                                </button>
                                <button
                                        :class="['px-6 py-2 rounded-full text-lg', selectedType === 'turns' ? 'bg-primary text-white' : 'bg-white hover:bg-primary-light text-gray-800']"
                                        @click="selectedType = 'turns'"
                                >
                                        TURNS
                                </button>
                        </div>

                </div>
                <!-- Scoreboard Table -->
                <div class="flex items-center justify-center w-full mb-8">
                        <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                                <thead>
                                        <tr class="bg-white">
                                                <th 
                                                        v-for="(header, index) in ['User', 'Board Type', 'Game Type', 'Turns Taken', 'Time Spent', 'Finished At']" 
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
                                                v-for="(game, index) in allGames" 
                                                :key="game.id" 
                                                :class="index % 2 === 0 ? 'bg-white' : 'bg-stone-200'"
                                                class="transition-colors duration-200 text-black"
                                        >
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

<style scoped>
/* Keyframe for full spin */
@keyframes spin {
0% {
        transform: rotate(0deg);
}
100% {
        transform: rotate(180deg);
}
}

/* Add the spinning animation class */
.animate-spin {
        animation: spin 0.5s linear;
}

/* Button disabled state */
button:disabled {
        pointer-events: none;
        opacity: 0.5;
}
</style>
