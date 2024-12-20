<script setup>
import { useErrorStore } from '@/stores/error';
import { useProfileStore } from '@/stores/profile';
import { computed, onMounted, ref } from 'vue';
import FormsInput from '../ui/forms/FormsInput.vue';

const storeError = useErrorStore()
const storeProfile = useProfileStore()

const props = defineProps({
    inUpdateMode: { type: Boolean, required: true }
})

const password = ref({
    old_password: null,
    password: null,
    password_confirmation: null
})

const resetPassword = () => {
    password.value.old_password = ''
    password.value.password = ''
    password.value.password_confirmation = ''
}

const updatePassword = async () => {
    const result = await storeProfile.updatePassword(password.value)
    if (result)
        resetPassword()
}

const isEmpty = (field) => password.value[field] && password.value[field].length > 0 ? 0 : 1
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
                <FormsInput v-model="password.old_password" input-type="password"
                            :error-message="storeError.fieldMessage('old_password')"
                            label="Current Password:" :as-errors="isEmpty('old_password')" />
                <FormsInput v-model="password.password" input-type="password"
                            :error-message="storeError.fieldMessage('password')"
                            label="New Password:" :as-errors="isEmpty('password')" />
                <FormsInput v-model="password.password_confirmation" input-type="password"
                            :error-message="storeError.fieldMessage('password_confirmation')"
                            label="Confirm new Password:" :as-errors="isEmpty('password_confirmation')" />
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