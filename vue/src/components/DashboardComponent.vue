<script setup>
import {useRouter} from 'vue-router';

const router = useRouter();

const goToHistory = () => {
        router.push('/history');
};

const goToSelectGameMode = () => {
        router.push('/selectGameMode');
};

const goToScoreboard = () => {
        router.push('/scoreboard');
};

</script>

<template>
    <div class="flex flex-col min-h-screen">

                <!-- Main content -->
                <div class="flex flex-col items-center justify-center flex-grow">
                        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 text-primary">
                                Memory Game
                        </h1>

                        <!-- Buttons -->
                        <div class="flex space-x-4">
                                <!-- Left Button -->
                                <button @click="goToHistory" class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                                        History
                                </button>
                                <!-- Center Button -->
                                <button @click="goToSelectGameMode" class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                                        Select Game Mode
                                </button>
                                <!-- Right Button -->
                                <button @click="goToScoreboard" class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                                        Scoreboard
                                </button>
                        </div>
                </div>
        </div>
</template>
