import { defineStore } from "pinia";
import { useErrorStore } from "./error";
import axios from "axios";
import { useAuthStore } from "./auth";


export const useProfileStore = defineStore('user', () => {
    const storeError = useErrorStore()
    const storeAuth = useAuthStore()

    const user = storeAuth.user

    const updateProfile = async (newProfile) => {
        try {
            const responseProfile = await axios.put('users/me', newProfile)
            user.value = responseProfile.data.data
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Profile Update Error!'
            )
            return false;
        }

    }     

})