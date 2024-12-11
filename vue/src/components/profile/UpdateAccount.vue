<script setup>
import { useAuthStore } from '@/stores/auth';
import { useErrorStore } from '@/stores/error';
import { useProfileStore } from '@/stores/profile';
import { onMounted, ref } from 'vue';
import FormsInput from "../ui/forms/FormsInput.vue";

const storeAuth = useAuthStore()
const storeError = useErrorStore()
const storeProfile = useProfileStore()

const props = defineProps({
    inUpdateMode: { type: Boolean, required: true }
})

const emit = defineEmits(['changeMode'])

const changeMode = () => {
    emit('changeMode')
}

const user = ref({})

const resetUser = () => {
    emit('changeMode')
    user.value = {
        name: storeAuth.user.name,
        email: storeAuth.user.email,
        nickname: storeAuth.user.nickname
    }
    storeError.resetMessages()
}

const updateProfile = async () => {
    const response = await storeProfile.updateProfile(user.value)
    if(response) {
        storeAuth.user.value = response
        resetUser()
    }
}

onMounted(() => {
    resetUser()
})

</script>

<template>
    <div
        class="w-full border-secondary-dark dark:border-secondary-light border-2 bg-gray-50 dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <div class="flex flex-col items-center font-semibold text-gray-50 text-2xl bg-primary py-6 px-32 space-y-3">
            My Profile
        </div>
        <div class="border-2 border-gray-900 dark:border-gray-100" />
        <form class="items-center flex flex-col">
            <div class="flex lg:flex-row flex-col-reverse content-center justify-between px-10">
                <div class="w-full flex flex-col justify-center content-center py-6 px-10 space-y-3">
                    <FormsInput v-model="user.name" label="Name" :errorMessage="storeError.fieldMessage('name')"
                        :readonly="!inUpdateMode" />
                    <FormsInput v-model="user.email" label="Email" placeholder="example@mail.com"
                        :errorMessage="storeError.fieldMessage('email')" :readonly="!inUpdateMode" />
                    <FormsInput v-model="user.nickname" label="Nickname"
                        :errorMessage="storeError.fieldMessage('nickname')" :readonly="!inUpdateMode" />
                </div>
                <div class="border-gray-900 dark:border-gray-100" />
                <div class="p-6 flex items-center justify-end mb-6 h-full w-1/2">
                    <img :src="storeAuth.userPhotoUrl" alt="Profile Picture"
                        class="w-full h-fit rounded-full object-cover border-8 border-double border-secondary-dark">
                </div>
            </div>
            <button v-show="!inUpdateMode" @click.prevent="changeMode"
                class="mb-5 text-lg font-semibold rounded-2xl bg-cyan-600 hover:bg-cyan-800 border-2 border-secondary-dark px-6 py-2">
                Edit profile
            </button>
            <div v-show="inUpdateMode" class="flex flex-row w-3/5 h-fit justify-between mb-5">
                <button @click.prevent="resetUser"
                    class="text-lg font-semibold rounded-2xl bg-transparent hover:bg-red-600 border-2 border-secondary-dark px-6 py-2">
                    Cancel
                </button>
                <button @click.prevent="updateProfile"
                    class="text-lg font-semibold rounded-2xl bg-lime-600 hover:bg-lime-800 border-2 border-secondary-dark px-6 py-2">
                    Save
                </button>
            </div>
        </form>
    </div>
</template>