<script setup>
import { useAuthStore } from "@/stores/auth"
import { ref } from "vue"
import ProfileLabel from "./ProfileLabel.vue"
import CoinsCard from "@/components/coins/CoinsCard.vue"
    
const storeAuth = useAuthStore()

const user = storeAuth.user
const password = ref({
    old_password: '',
    password: '',
    password_confirmation: ''
})

const update = false

const coins = ref(10)

</script>

<template>
  <div class="flex flex-row items-center justify-content">
    <div class="w-3/5 mx-auto p-8">
        <div
            class="border-secondary-dark dark:border-secondary-light border-2 bg-gray-200 dark:bg-gray-700 shadow-lg rounded-lg overflow-hidden">
            <div class="flex flex-col items-center bg-gray-400 dark:bg-primary-dark py-6 px-32 space-y-3">
                My Profile
            </div>
            <div class="border-2 border-gray-900 dark:border-gray-100" />
            <form class="flex lg:flex-row flex-col-reverse content-center justify-between px-10">
                <div class="w-full flex flex-col content-center bg-gray-200 dark:bg-primary-dark py-6 space-y-3">
                    <ProfileLabel v-model="user.email" label="Email" placeholder="example@mail.com"
                        :readonly="!update" />
                    <ProfileLabel v-model="user.nickname" label="Nickname" :readonly="!update" />
                    <ProfileLabel v-model="user.name" label="Name" :readonly="!update" />
                </div>
                <div class=" border-gray-900 dark:border-gray-100" />
                <div class="p-6 flex-col space-y-5 content-center">
                    <div class="flex justify-center mb-6">
                        <img :src="storeAuth.userPhotoUrl" alt="Profile Picture"
                            class="w-50 h-50 rounded-full object-cover">
                    </div>
                </div>
            </form>
            <div v-show="update" class="border-t-2 border-gray-900 dark:border-gray-100" />
            <form v-show="update">
                <div class="flex flex-col items-center bg-gray-200 dark:bg-primary-dark py-6 px-32">
                    <ProfileLabel v-model="password.old_password" label="Current password" placeholder="password"
                        inputType="password" />
                    <ProfileLabel v-model="password.password" label="New password" placeholder="password"
                        inputType="password" />
                    <ProfileLabel v-model="password.password_confirmation" label="Confirm password"
                        placeholder="password" inputType="password" />
                </div>
            </form>
        </div>
    </div>
    <CoinsCard/>
  </div>
</template>
