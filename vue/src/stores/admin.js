import { ref } from 'vue'
import { defineStore } from "pinia";
import axios from 'axios';
import { useErrorStore } from "./error";

export const useAdminStore = defineStore('admin', () => {

        const admin = ref(null);

        const storeError = useErrorStore();

        const createAdmin = async (admin) => {
                storeError.resetMessages()
                try {
                        const formData = new FormData()

                        //add image if any to admin
                        if (admin.image)
                        formData.append('photo_image', admin.image)

                        formData.append('name', admin.name)
                        formData.append('email', admin.email)
                        formData.append('nickname', admin.nickname)
                        formData.append('password', admin.password)
                        formData.append('password_confirmation', admin.password_confirm)

                        const response = await axios.post('admins', formData, {
                                headers: {"Content-Type": "multipart/form-data"}
                        })
                        return response.data;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Admin creation Error!'
                        )
                        return false
                }
        }

        return { admin, createAdmin };
})
