<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import FormsInput from './ui/forms/FormsInput.vue';
import { useErrorStore } from '@/stores/error';
import FormsImage from './ui/forms/FormsImage.vue';
import avatarNoneAssetURL from '@/assets/avatar-none.png'

const storeAuth = useAuthStore()
const storeError = useErrorStore()
const router = useRouter()

const user = ref({
    name: null,
    email: null,
    nickname: null,
    password: null,
    password_confirm: null,
    image: null
})

const submit = async () => {
    const result = await storeAuth.registerAccount(user.value)
    if(result)
        router.push({ name: 'home' })
}

const imageUpload = (file) => user.value.image = file

//Small validations
const isEmpty = (field) => user.value[field] && user.value[field].length > 0 ? 0 : 1
const isPasswordValid = computed(() => user.value.password && user.value.password.length >= 3 ? 0 : 1)
const isConfirmPasswordValid = computed(() => user.value.password_confirm && user.value.password_confirm == user.value.password ? 0 : 1)

const passwordError = computed(() => {
    const em = storeError.fieldMessage('password')
    if(em.length > 0)
        return em
    return user.value.password != null && isPasswordValid.value == 1 ? 'Password must have at least 3 characters' : null
})

const confirmPasswordError = computed(() => {
    const em = storeError.fieldMessage('password_confirmation')
    if(em.length > 0)
        return em
    return user.value.password_confirm != null && isConfirmPasswordValid.value == 1 ? 'Passwords do not match' : null
})

</script>

<template>
    <div class="flex items-center justify-center p-10">
        <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold text-center text-primary">Register</h2>
            <p class="text-center text-gray-600">Create an account</p>
            <form class="mt-6 justify-center items-center space-y-10">
                <div class="flex flex-col w-full h-fit items-center">
                    <div class="flex w-fit h-min items-center">
                        <FormsImage @image-upload="imageUpload" label="Have a photo or avatar ready?" :placeholder="avatarNoneAssetURL"
                             :error-message="storeError.fieldMessage('photo_image')" />
                    </div>
                    <hr class="border-secondary w-2/3 mb-5">
                    <div class="flex flex-col space-y-5 w-full h-fit items-center">
                        <FormsInput v-model="user.email" placeholder="example@mail.pt" input-type="email"
                            :error-message="storeError.fieldMessage('email')" label="Your email:" :as-errors="isEmpty('email')" />
                        <FormsInput v-model="user.name" placeholder="John Doe"
                            :error-message="storeError.fieldMessage('name')" label="Your name:" :as-errors="isEmpty('name')" />
                        <FormsInput v-model="user.nickname" placeholder="john"
                            :error-message="storeError.fieldMessage('nickname')" label="Nickname:" :as-errors="isEmpty('nickname')" />
                        <FormsInput v-model="user.password" input-type="password"
                            :error-message="passwordError"
                            label="Password:" :as-errors="isPasswordValid" />
                        <FormsInput v-model="user.password_confirm" input-type="password"
                            :error-message="confirmPasswordError"
                            label="Confirm Password:" :as-errors="isConfirmPasswordValid+isEmpty('password_confirm')"/>
                    </div>
                </div>
                <button @click.prevent="submit" type="submit"
                    class="w-full px-4 py-2 mt-6 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light">Register</button>
            </form>
            <p class="mt-4 text-sm text-center text-gray-600">
                Already have an account?
                <RouterLink to="/login" class="text-tertiary font-medium hover:underline">Login</RouterLink>
            </p>

        </div>
    </div>
</template>
