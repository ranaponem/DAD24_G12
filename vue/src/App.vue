<script setup>
import Toaster from './components/ui/toast/Toaster.vue'
import ProfileMini from './components/profile/ProfileMini.vue'
import { useAuthStore } from './stores/auth';
import { computed } from 'vue';

const storeAuth = useAuthStore()
const isAdmin = computed(() => storeAuth.userType === 'A');
console.log(isAdmin)
</script>

<template>
  <div>
    <Toaster />
    <div class="min-h-screen bg-gray-50 flex flex-col">
      <!-- Navbar -->
      <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <nav class="flex items-center justify-between h-16">
            <div class="flex items-center justify-start self-stretch space-x-8">
              <RouterLink to="/"
                class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600 font-semibold">
                Home
              </RouterLink>
              <RouterLink to="/games"
                class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600 font-semibold">
                Play
              </RouterLink>
              <RouterLink to="/scoreboards"
                class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600 font-semibold">
                Scoreboards
              </RouterLink>
              <RouterLink v-if="isAdmin" to="/administration"
                class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600 font-semibold">
                Administration
              </RouterLink>
            </div>
            <ProfileMini
              class="justify-self-end text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              active-class="text-blue-600 font-semibold">
            </ProfileMini>
          </nav>
        </div>
      </header>
      <div class="border-2 border-gray-400" />

      <!-- Main content section where RouterView renders -->
      <main class="flex-grow bg-gray-50 dark:bg-gray-800">
        <RouterView />
      </main>
    </div>
  </div>
</template>
