<script setup>
import { onMounted, ref, computed } from 'vue';
import { useUsersStore } from '@/stores/users';

const usersStore = useUsersStore();
const allUsers = ref([]);
const meta = ref();
const pageNum = ref(1);
const totalPages = ref(1);
const selectedScope = ref('Players'); // Default to "Players"
const isSpinning = ref(false);  // Track if the button is spinning

const fetchUsers = async () => {
        try {
                const response =
                        selectedScope.value === 'Players'
                                ? await usersStore.getPlayers(pageNum.value)
                                : await usersStore.getAdmins(pageNum.value);
                allUsers.value = response.data.data;
                meta.value = response.data.meta;
                totalPages.value = meta.value.last_page;
        } catch (error) {
                console.error('Error fetching users:', error);
        }
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
                fetchUsers();
        }, 700);
};

// Handle switching between Players and Admins
const handleButtonClick = () => {
        if (isSpinning.value) return; // Prevent click if already spinning
        isSpinning.value = true;
        selectedScope.value = selectedScope.value === 'Players' ? 'Admins' : 'Players';
        pageNum.value = 1; // Reset to first page on switch
        fetchUsers();
        setTimeout(() => {
                isSpinning.value = false;
        }, 500); // Match the duration of the animation (1 second)
};

onMounted(() => {
        fetchUsers();
});

// Dynamically update headers based on selected scope
const getHeaders = computed(() => {
        if (selectedScope.value === 'Players') {
                return ['Nickname', 'Name', 'Email', 'Brain Coins', 'Blocked'];
        } else {
                return ['Nickname', 'Name', 'Email']; // Hide Brain Coins and Blocked for Admins
        }
});
</script>

<template>
        <div class="flex flex-col items-center justify-center flex-grow">
                <!-- Title -->
                <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-4 mt-8 text-primary">
                        Administration Table
                </h1>

                <!-- GLOBAL/PERSONAL Switch -->
                <div class="flex items-center justify-center space-x-4 mt-2 mb-6">
                        <span
                                :class="[
                                        'text-xl font-semibold cursor-pointer',
                                        selectedScope === 'Players' ? 'text-primary' : 'text-gray-500'
                                ]"
                                @click="selectedScope !== 'Players' && handleButtonClick"
                        >
                                Players
                        </span>
                        <button
                                :class="[
                                        'w-16 h-16 p-2 bg-primary rounded-full hover:bg-primary-light focus:outline-none transition-all duration-200',
                                        { 'animate-spin' : isSpinning}
                                ]"
                                @click="handleButtonClick"
                                class="cursor-pointer"
                        >
                                <svg
                                        class="object-cover w-full h-full"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                >
                                        <path
                                        d="M1,12A11,11,0,0,1,17.882,2.7l1.411-1.41A1,1,0,0,1,21,2V6a1,1,0,0,1-1,1H16a1,1,0,0,1-.707-1.707l1.128-1.128A8.994,8.994,0,0,0,3,12a1,1,0,0,1-2,0Zm21-1a1,1,0,0,0-1,1,9.01,9.01,0,0,1-9,9,8.9,8.9,0,0,1-4.42-1.166l1.127-1.127A1,1,0,0,0,8,17H4a1,1,0,0,0-1,1v4a1,1,0,0,0,.617.924A.987.987,0,0,0,4,23a1,1,0,0,0,.707-.293L6.118,21.3A10.891,10.891,0,0,0,12,23,11.013,11.013,0,0,0,23,12,1,1,0,0,0,22,11Z"
                                />
                                </svg>
                        </button>
                        <span
                                :class="[
                                        'text-xl font-semibold cursor-pointer',
                                        selectedScope === 'Admins' ? 'text-primary' : 'text-gray-500'
                                ]"
                                @click="selectedScope !== 'Admins' && handleButtonClick"
                        >
                                Admins
                        </span>
                </div>

                <!-- Users Table -->
                <div class="flex items-center justify-center w-full mb-8">
                        <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                                <thead>
                                        <tr class="bg-white">
                                                <th
                                                        v-for="(header, index) in getHeaders"
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
                                                v-for="(user, index) in allUsers"
                                                :key="user.id"
                                                :class="[
                                                        user.blocked ? 'bg-red-200' : index % 2 === 0 ? 'bg-white' : 'bg-stone-200',
                                                        'transition-colors duration-200 text-black'
                                                ]"
                                        >
                                                <td class="px-4 py-2">{{ user.nickname }}</td>
                                                <td class="px-4 py-2">{{ user.name }}</td>
                                                <td class="px-4 py-2">{{ user.email }}</td>
                                                <td
                                                        v-if="selectedScope === 'Players'"
                                                        class="px-4 py-2"
                                                >
                                                        {{ user.brain_coins_balance }}
                                                </td>
                                                <td
                                                        v-if="selectedScope === 'Players'"
                                                        class="px-4 py-2"
                                                >
                                                        <svg
                                                                v-if="user.blocked"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="red"
                                                                viewBox="0 0 24 24"
                                                                class="w-6 h-6 mx-auto"
                                                        >
                                                                <path
                                                                d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm5-11a1 1 0 0 0-1-1H8a1 1 0,0,0,0,2h8a1,1,0,0,0,1-1z"
                                                        />
                                                        </svg>
                                                        <svg
                                                                v-else
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="green"
                                                                viewBox="0 0 24 24"
                                                                class="w-6 h-6 mx-auto"
                                                        >
                                                                <path
                                                                d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-5.414l7.707-7.707a1,1,0,0,0-1.414-1.414L11 14.586 7.707 11.293a1,1,0,1,0-1.414,1.414L11 16.586a1,1,0,0,0,1.414,0z"
                                                        />
                                                        </svg>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-center mt-6">
                        <button
                                class="px-4 py-2 text-white rounded-l-lg"
                                :class="pageNum === 1 ? 'bg-primary-dark' : 'bg-primary'"
                                :disabled="pageNum === 1"
                                @click="pageNum > 1 && (pageNum--, fetchUsers())"
                        >
                                Previous
                        </button>
                        <input
                        type="text"
                        v-model.number="pageNum"
                        @input="handlePageChange"
                        class="ml-2 py-1 text-center text-xl border border-gray-300 rounded"
                        style="width: 50px; text-align: center;"
                />
                        <span class="px-4 text-lg">of {{ totalPages }}</span>
                        <button
                                class="px-4 py-2 text-white rounded-r-lg"
                                :class="pageNum === totalPages ? 'bg-primary-dark' : 'bg-primary'"
                                :disabled="pageNum === totalPages"
                                @click="pageNum < totalPages && (pageNum++, fetchUsers())"
                        >
                                Next
                        </button>
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
