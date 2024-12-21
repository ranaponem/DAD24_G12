<script setup>
import { onMounted, ref, watch } from 'vue';
import { useGamesStore } from '@/stores/games';
import { useAuthStore } from '../stores/auth';

const gamesStore = useGamesStore()
const storeAuth = useAuthStore()

const selectedBoard = ref('ALL');
const selectedGameType = ref('S');
const pageNum = ref(1);
const totalPages = ref(1);

const fetchHistory = async () => {
    try {
        await gamesStore.getHistory(pageNum.value, selectedBoard.value, selectedGameType.value);
        console.log(gamesStore.myGames)
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

const getBoardType = (boardId) => {
    const boardTypes = {
        1: '3x4',
        2: '4x4',
        3: '6x6'
    };
    return boardTypes[boardId] || 'Unknown';
};

const getGameType = (type) => {
    const gameTypes = {
        S: 'Single Player',
        M: 'Multiplayer'
    };
    return gameTypes[type] || 'Unknown';
};

const getStatusType = (status) => {
    const statusTypes = {
        E: 'Ended',
        I: 'Interrupted',
        PE: 'Pending',
        PL: 'Progress'
    };
    return statusTypes[status] || 'Unknown';
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

watch([selectedBoard, selectedGameType], fetchHistory);

onMounted(() => {
    fetchHistory();
});

</script>

<template>
    <div class="flex flex-col items-center justify-center flex-grow">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 mt-8 text-primary">
            My Game History
        </h1>
        <div class="flex flex-row">
            <div
                class="bg-white p-4 mb-4 mr-4 flex items-center space-x-2 rounded-3xl border-2 border-primary justify-center">
                <label for="boardType" class="text-lg font-bold text-gray-700 mr-2">Filter by Board Type:</label>
                <select id="boardType" v-model="selectedBoard" @change="pageNum = 1"
                    class="p-2 border rounded-lg bg-white shadow-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="ALL">ALL</option>
                    <option value="3x4">3x4</option>
                    <option value="4x4">4x4</option>
                    <option value="6x6">6x6</option>
                </select>
            </div>
            <div
                class="ml-4 bg-white p-4 mb-4 flex items-center space-x-2 rounded-3xl border-2 border-primary justify-center">
                <button
                    :class="['px-6 py-2 rounded-full text-lg', selectedGameType === 'S' ? 'bg-primary text-white' : 'bg-white hover:bg-primary-light text-gray-800']"
                    @click="selectedGameType = 'S', pageNum = 1">
                    SinglePlayer
                </button>
                <button
                    :class="['px-6 py-2 rounded-full text-lg', selectedGameType === 'M' ? 'bg-primary text-white' : 'bg-white hover:bg-primary-light text-gray-800']"
                    @click="selectedGameType = 'M', pageNum = 1">
                    MultiPlayer
                </button>
            </div>
        </div>
        <div class="flex items-center justify-center w-full">
            <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-white">
                        <th v-for="(header, index) in ['Board Type', 'Game Type', 'Turns Taken', 'Time Spent', 'Finished At', 'Status']"
                            :key="index" class="bg-primary px-4 py-2 text-xl text-stone-900"
                            :style="{ minWidth: '150px' }">
                            {{ header }}
                        </th>
                        <th v-if="selectedGameType == 'M'" class="bg-primary px-4 py-2 text-xl text-stone-900"
                            :style="{ minWidth: '150px' }">
                            Created by
                        </th>
                        <th v-if="selectedGameType == 'M'" class="bg-primary px-4 py-2 text-xl text-stone-900"
                            :style="{ minWidth: '150px' }">
                            Winner
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Check if there are no games -->
                    <tr v-if="gamesStore.myGames.length === 0">
                        <td colspan="5" class="px-4 py-8 text-2xl font-bold text-red-600 bg-red-100 rounded-b-lg">
                            You haven't played any games!
                        </td>
                    </tr>
                    <tr v-else v-for="(game, index) in gamesStore.myGames" :key="game.id"
                        :class="index % 2 === 0 ? 'bg-white' : 'bg-stone-200'"
                        class="transition-colors duration-200 text-black">
                        <td class="px-4 py-2">{{ getBoardType(game.board.id) }}</td>
                        <td class="px-4 py-2">{{ getGameType(game.type) }}</td>
                        <td class="px-4 py-2">
                            {{ game.status === 'E' ? game.total_turns : '---' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ game.status === 'E' ? game.total_time : '---' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ game.status === 'E' ? formatDate(game.ended_at) : '---' }}
                        </td>
                        <td class="px-4 py-2">{{ getStatusType(game.status) }}</td>
                        <td v-if="selectedGameType == 'M'" class="px-4 py-2">
                            {{ storeAuth.user.id == game.created_by?.id ? 'You' : game.created_by.nickname }}
                        </td>
                        <td v-if="selectedGameType == 'M'" class="px-4 py-2">
                            {{ storeAuth.user.id == game.won_by?.id ? 'You' : game.won_by.nickname }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-center mt-6">
            <button class="px-4 py-2 text-white rounded-l-lg" :class="pageNum === 1 ? 'bg-primary-dark' : 'bg-primary'"
                :disabled="pageNum === 1" @click="pageNum > 1 && (pageNum--, fetchHistory())">
                Previous
            </button>
            <input type="text" v-model.number="pageNum" @input="handlePageChange"
                class="ml-2 py-1 text-center text-xl border border-gray-300 rounded" :min="1" :max="totalPages"
                inputmode="numeric" pattern="[0-9]*" :maxlength="totalPages.toString().length"
                style="width: 50px; text-align: center;" />
            <span class="px-2 py-2 text-xl">of {{ totalPages }}</span>
            <button class="px-4 py-2 text-white rounded-r-lg"
                :class="pageNum === totalPages ? 'bg-primary-dark' : 'bg-primary'" :disabled="pageNum === totalPages"
                @click="pageNum < totalPages && (pageNum++, fetchHistory())">
                Next
            </button>
        </div>
    </div>
</template>
