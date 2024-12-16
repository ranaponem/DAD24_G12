<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth';
import { Button } from '@/components/ui/button'


const authStore = useAuthStore()

const email = ref('p2@mail.pt')
const password = ref('123')
const responseData = ref('')

const submit = async () => {

    const user = await authStore.login({
        email: email.value,
        password: password.value
    })
    responseData.value = user.name

}
</script>

<template>
    <div class="flex items-center justify-center p-10">
        <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold text-center text-primary">Welcome Back</h2>
            <p class="text-center text-gray-600">Login to your account</p>
            <form class="mt-6">
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
                <div class="mb-4">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-600">
                        Password:
                    </label>
                    <input
                    type="password"
                    id="password"
                    placeholder="Enter your password"
                    v-model="password"
                    class="w-full px-4 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light focus:border-transparent"
                    />
                </div>
                <!-- <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" class="mr-2 focus:ring-tertiary-light" />
                    Remember me
                    </label>
                    <a href="#" class="text-sm text-tertiary hover:underline">
                    Forgot Password?
                    </a>
                    </div> -->
                <button @click.prevent="submit" type="submit"
                    class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">Login</button>
            </form>
            <!--<p class="mt-4 text-sm text-center text-gray-600">
                Don't have an account? 
                <a href="#" class="text-tertiary font-medium hover:underline">Sign up</a>
                </p>-->

        </div>
    </div>
</template>
