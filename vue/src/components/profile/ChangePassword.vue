<script setup>
import { useErrorStore } from '@/stores/error';
import { useProfileStore } from '@/stores/profile';
import { onMounted, ref } from 'vue';
import FormsInput from '../ui/forms/FormsInput.vue';

const storeError = useErrorStore()
const storeProfile = useProfileStore()

const props = defineProps({
    inUpdateMode: { type: Boolean, required: true }
})

const password = ref({})

const resetPassword = () => {
    password.value = {
        old_password: '',
        password: '',
        password_confirmation: ''
    }
    storeError.resetMessages()
}

const updatePassword = async () => {
    const result = await storeProfile.updatePassword(password.value)
    if (result)
        resetPassword()
}

onMounted(() => {
    resetPassword()
})
</script>

<template>
    <div v-show="inUpdateMode"
        class="w-full border-secondary-dark dark:border-secondary-light border-2 bg-gray-50 dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <div
            class="flex flex-col items-center font-semibold text-gray-50 text-2xl bg-tertiary-dark py-6 px-32 space-y-3">
            Change password
        </div>
        <div class="border-2 border-gray-900 dark:border-gray-100" />
        <form class="items-center flex flex-col">
            <div class="flex flex-col items-center space-y-3 py-6 px-32 w-full">
                <FormsInput v-model="password.old_password" label="Current password" placeholder="password"
                    inputType="password" :errorMessage="storeError.fieldMessage('old_password')" />
                <FormsInput v-model="password.password" label="New password" placeholder="password" inputType="password"
                    :errorMessage="storeError.fieldMessage('password')" />
                <FormsInput v-model="password.password_confirmation" label="Confirm password" placeholder="password"
                    inputType="password" :errorMessage="storeError.fieldMessage('password_confirmation')" />
            </div>
            <div v-show="inUpdateMode" class="flex flex-row w-3/5 h-fit justify-between pb-5">
                <button @click.prevent="resetPassword"
                    class="text-lg font-semibold rounded-2xl bg-transparent hover:bg-red-600 border-2 border-secondary-dark px-6 py-2">
                    Clear
                </button>
                <button @click.prevent="updatePassword"
                    class="text-lg font-semibold rounded-2xl bg-lime-600 hover:bg-lime-800 border-2 border-secondary-dark px-6 py-2">
                    Save
                </button>
            </div>
        </form>
    </div>
</template>