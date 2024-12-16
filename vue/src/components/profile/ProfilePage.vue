<script setup>
import UpdateAccount from "./UpdateAccount.vue";
import ChangePassword from "./ChangePassword.vue";
import DeleteAccount from "./DeleteAccount.vue";
import { onMounted, ref } from "vue";
import { useAuthStore } from "@/stores/auth";

import CoinsCard from "@/components/coins/CoinsCard.vue"
const storeAuth = useAuthStore()

const inUpdateMode = ref(false)

const changeMode = () => {
    inUpdateMode.value = !inUpdateMode.value
}

onMounted(() => {
    inUpdateMode.value = false
})

</script>

<template>
    <div class="flex flex-col items-center max-w-5xl mx-auto p-8">
        <UpdateAccount @changeMode="changeMode" :inUpdateMode="inUpdateMode" />
        <div v-show="inUpdateMode" class="py-6 h-full w-2/5 border-y-0 border-x-2 border-black dark:border-white"/>
        <ChangePassword :inUpdateMode="inUpdateMode" />
        <div v-show="!inUpdateMode" class="py-6 h-fit w-2/5 border-y-0 border-x-2 border-black dark:border-white text-center items-center justify-center">
            <svg class="w-full h-20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="12" cy="17" r="1" fill="#000000"></circle> <path d="M12 10L12 14" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M3.44722 18.1056L10.2111 4.57771C10.9482 3.10361 13.0518 3.10362 13.7889 4.57771L20.5528 18.1056C21.2177 19.4354 20.2507 21 18.7639 21H5.23607C3.7493 21 2.78231 19.4354 3.44722 18.1056Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
            <span class="mb-5 h-fit">Danger zone</span>
        </div>
        <DeleteAccount v-show="!inUpdateMode && storeAuth.userType == 'P'" :inUpdateMode="inUpdateMode" />
    </div>
    <CoinsCard/>
  </div>
</template>
