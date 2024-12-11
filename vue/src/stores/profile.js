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

    const user = storeAuth.user
    const temp = ref({})

    const updateProfile = async (newProfile) => {
        try {
            const responseProfile = await axios.put('users/me', newProfile)
            user.value = responseProfile.data.data
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

    const deleteAccount = async (credentials) => {
        const result = await storeAuth.deleteAccount(credentials)
        if (result == true) {
            toast({
                title: 'Account deletion',
                description: 'Account was successfully deleted.',
                variant: 'information'
            })
            return true
        }
          storeError.setErrorMessages(
            result.response.data.message,
            result.response.data.errors,
            result.response.status,
            'DEBUG'
          )
        /*toast({
            title: 'Account deletion Error!',
            description: 'The password provided is invalid',
            variant: 'destructive'
        })*/
        return false
    }

    return {
        updateProfile,
        updatePassword,
        deleteAccount,
        temp
    }
})