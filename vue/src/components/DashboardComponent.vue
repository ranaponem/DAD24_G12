<script setup>
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();

const isAdmin = authStore.userType == 'A';
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <!-- Main content -->
        <div class="flex flex-col items-center justify-center flex-grow">
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 text-primary">
                Memory Game
            </h1>

            <!-- Buttons -->
            <div class="flex flex-col items-center justify-center w-2/3 md:w-1/2 lg:w-2/5 xl:w-1/5 space-y-4">
                <!-- Left Button -->
                <RouterLink to="/history"
                    class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    History
                </RouterLink>
                <!-- Center Button -->
                <RouterLink to="/games"
                    class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Select Game Mode
                </RouterLink>
                <!-- Right Button -->
                <RouterLink to="/scoreboard"
                    class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Scoreboard
                </RouterLink>
                <RouterLink to="/transactions"
                    class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Transactions
                </RouterLink>
                <RouterLink v-if="isAdmin" to="/statistics"
                    class="w-full px-4 py-2 mt-6 text-white bg-secondary-light hover:bg-secondary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Statistics
                </RouterLink>
                <RouterLink v-if="isAdmin" to="/administration"
                    class="w-full px-4 py-2 mt-6 text-white bg-secondary-light hover:bg-secondary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Administration
                </RouterLink>
            </div>
        </div>
    </div>
</template>
