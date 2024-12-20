<script setup>
import { useAuthStore } from "@/stores/auth";
import { ref } from "vue";
import { useRouter } from "vue-router";
    
const storeAuth = useAuthStore()
const router = useRouter()

const showLinks = ref(false)

const showLinksMenu = () => {
    showLinks.value = !showLinks.value
}

const unshowLinksMenu = () => {
    showLinks.value = false
}

const logout = async () => {
    unshowLinksMenu()
    const result = await storeAuth.logout()    
    if(result)
        router.push({ name: 'home' })
}

</script>

<template>
    <div>
        <div v-if="storeAuth.user" class="relative">
            <button @click.prevent="showLinksMenu" class="flex items-center space-x-4">
                <span class="truncate w-100 font-semibold text-lg text-gray-800 dark:text-gray-300 cursor-pointer">
                    {{storeAuth.userFirstLastName}}
                </span>
                <img :src="storeAuth.userPhotoUrl" alt="Profile Picture"
                    class="w-12 h-12 rounded-full object-cover cursor-pointer" />
            </button>
            <div v-show="showLinks" class="absolute flex flex-col items-center mt-3 bg-gray-100 w-full p-4 rounded-b shadow-md">
                <RouterLink @click.prevent="unshowLinksMenu" :to="{ name: 'profile' }" class="block text-gray-700 font-semibold py-2 hover:text-gray-900">
                    Profile
                </RouterLink>
                <hr class="my-4 w-full border-gray-300" />
                <button @click="logout"
                    class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                    Logout
                </button>
            </div>
        </div>
        <RouterLink class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            active-class="text-blue-600 font-semibold" v-else :to="{ name: 'login' }">
            Login
        </RouterLink>
    </div>
</template>