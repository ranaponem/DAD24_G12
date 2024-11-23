<script setup>
    import { ref } from 'vue'

   
</script>

<template>
    <div class="mb-4">
        <label for="email" class="block mb-2 text-sm font-medium text-gray-600">
            Email:
        </label>
        <input
        type="email"
        id="email"
        placeholder="Enter your email"
        v-model="email"
        class="w-full px-4 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light focus:border-transparent"
        />
    </div>
</template>
