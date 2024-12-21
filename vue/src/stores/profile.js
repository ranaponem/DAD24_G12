import { defineStore } from "pinia";
import { useErrorStore } from "./error";
import axios from "axios";
import { useAuthStore } from "./auth";
import { useToast } from "@/components/ui/toast";
import { ref } from "vue";


export const useProfileStore = defineStore('user', () => {
    const { toast } = useToast()
    const storeError = useErrorStore()
    const storeAuth = useAuthStore()

    const user = ref(storeAuth.user)

    const updateProfile = async (userPayload) => {
        storeError.resetMessages()
        try {
            const formData = new FormData()

            if (userPayload.image)
                formData.append('photo_image', userPayload.image)
            
            formData.append('_method', 'put')
            formData.append('name', userPayload.name)
            formData.append('email', userPayload.email)
            formData.append('nickname', userPayload.nickname)

            const responseProfile = await axios.post('users/me', formData, {
                headers: {"Content-Type": "multipart/form-data"},
                withCredentials: true
            })
            storeAuth.user = responseProfile.data.data
            toast({
                title: 'Profile update',
                description: 'Profile has been updated successfully.',
                variant: 'info'
            })
            return user.value
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Profile Update Error!'
            )
            return false
        }
    }

    const updatePassword = async (newPassword) => {
        storeError.resetMessages()
        try {
            await axios.put('users/updatepassword', newPassword)
            toast({
                title: 'Password change',
                description: 'Password has changed successfully.',
                variant: 'info'
            })
            return true
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Password Change Error!'
            )
            return false
        }
    }


    return {
        updateProfile,
        updatePassword
    }
})
