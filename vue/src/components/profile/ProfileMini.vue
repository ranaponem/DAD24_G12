<script setup>
import { useAuthStore } from "@/stores/auth";
    
const storeAuth = useAuthStore()

</script>

<template>
    <div>
        <div v-if="storeAuth.user" class="flex items-center space-x-4">
            <span class="truncate w-100 font-semibold text-lg text-gray-800 dark:text-gray-300 cursor-pointer">
                {{storeAuth.userName}}
            </span>
            <RouterLink :to="{ name: 'profile' }">
                <img :src="storeAuth.userPhotoUrl" alt="Profile Picture" class="w-12 h-12 rounded-full object-cover cursor-pointer"/>
            </RouterLink>
        </div>
        <RouterLink v-else :to="{ name: 'login' }">
            Login
        </RouterLink>
    </div>
</template>