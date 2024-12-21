<script setup>
import { useRouter } from 'vue-router';

</script>

<template>
        <div class="flex h-full max-h-screen w-full mt-60">

                <!-- Main content -->
                <div class="flex flex-col items-center justify-center flex-grow">
                        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 text-primary">
                                Memory Game
                        </h1>

                        <!-- Buttons -->
                        <div class="flex flex-col items-center justify-center w-2/3 md:w-1/2 lg:w-2/5 xl:w-1/5 space-y-4">
                                <!-- Left Button -->
                                <RouterLink to="/history"
                                        class="w-full text-center px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                                        History
                                </RouterLink>
                                <!-- Center Button -->
                                <RouterLink to="/games"
                                        class="w-full text-center px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                                        Play games
                                </RouterLink>
                                <!-- Right Button -->
                                <RouterLink to="/scoreboard"
                                        class="w-full text-center px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                                        Scoreboard
                                </RouterLink>
                        </div>
                </div>
        </div>
</template>
