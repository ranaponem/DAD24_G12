<script setup>
import Toaster from './components/ui/toast/Toaster.vue'
import ProfileMini from './components/profile/ProfileMini.vue'
import { useAuthStore } from './stores/auth';

const storeAuth = useAuthStore()
</script>

<template>
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
          </div>
          <ProfileMini
            class="justify-self-end text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            active-class="text-blue-600 font-semibold">
          </ProfileMini>
        </nav>
      </div>
    </header>
    <div class="border-2 border-gray-400"/>

    <!-- Main content section where RouterView renders -->
    <main class="flex-grow bg-gray-200 dark:bg-gray-800">
        <RouterView />
    </main>
  </div>
</template>
