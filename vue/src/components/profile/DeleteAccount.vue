<script setup>
import { useProfileStore } from '@/stores/profile';
import { useErrorStore } from '@/stores/error';
import { onMounted, ref } from 'vue';
import FormsInput from '../ui/forms/FormsInput.vue';

const storeError = useErrorStore()
const storeProfile = useProfileStore()

const deleteModalStatus = ref(0)
const pws = ref("")

const resetModal = () => {
    deleteModalStatus.value = 0
    pws.value = ""
}
const modalAppear = () => deleteModalStatus.value = 1
const modalRequirePassword = () => {
    deleteModalStatus.value++
    if(deleteModalStatus.value > 2) {
        storeProfile.deleteAccount(...pws.value)
        resetModal()
    }
}

onMounted(() => {
    resetModal()
})

</script>

<template>
    <div
        class="w-1/2 border-secondary-dark dark:border-secondary-light border-2 bg-gray-50 dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <div
            class="flex flex-col items-center font-semibold text-gray-50 text-2xl bg-tertiary-dark py-6 px-32 space-y-3">
            Delete account
        </div>
        <div class="border-2 border-gray-900 dark:border-gray-100" />
        <div class="items-center flex flex-col py-4">
            <button @click.prevent="modalAppear"
                class="text-lg font-semibold rounded-2xl bg-red-700 hover:bg-red-800 border-2 border-secondary-dark px-6 py-2">
                Delete
            </button>
        </div>
        <form v-show="deleteModalStatus > 0"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white h-fit max-h-fit rounded-lg shadow-lg p-6 w-full max-w-xl flex flex-col justify-evenly items-center">
                <div class="flex flex-col items-center py-6">
                    <h1 class="font-semibold w-fit h-fit text-2xl">Delete account?</h1>
                    <p class="text-lg text-center">Are you sure you want to delete your account?</p>
                    <p class="text-sm underline text-center">You will lose all your brain coins and lose access to this account</p>
                </div>
                <div v-show="deleteModalStatus == 2" class="flex flex-col w-3/4 h-fit pb-10 items-center">
                    <span>Current Password</span>
                    <FormsInput v-model="pws" />
                </div>
                <div class="flex justify-between w-full h-fit px-2">
                    <button @click.prevent="resetModal"
                        class="text-lg font-semibold rounded-2xl bg-transparent hover:bg-red-600 border-2 border-secondary-dark px-6 py-2">
                        Cancel
                    </button>
                    <button @click.prevent="modalRequirePassword"
                        class="text-lg font-semibold rounded-2xl bg-red-600 hover:bg-red-800 border-2 border-secondary-dark px-6 py-2">
                        Delete
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
