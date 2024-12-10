<script setup>
import Button from './ui/button/Button.vue';
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <!-- Logo -->
        <div class="flex w-full justify-start px-4 mt-4">
            <img src="/estg_h-01.png" alt="ESTG Logo" class="w-40 h-auto object-contain" />
        </div>

        <!-- Main content -->
        <div class="flex flex-col items-center justify-center flex-grow">
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl text-center mb-8 text-primary">
                Memory Game
            </h1>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <!-- Left Button -->
                <button class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    History
                </button>
                <!-- Center Button -->
                <RouterLink to="/games" class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Select Game Mode
                </RouterLink>
                <!-- Right Button -->
                <button class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">
                    Scoreboards
                </button>
            </div>
        </div>
    </div>
</template>
