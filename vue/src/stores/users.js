import { ref } from 'vue';
import { defineStore } from "pinia";
import axios from 'axios';
import { useErrorStore } from "./error";

export const useUsersStore = defineStore('users', () => {

        const storeError = useErrorStore()

        const allUsers = ref([]) 
        const allPlayers = ref([]) 
        const allAdmins = ref([]) 
        const meta = ref({})

        const getPlayers = async (pageNum) => {
                let params = new URLSearchParams(); 

                params.append('page', pageNum); 

                try {
                        const responseUsers = await axios.get('/users', { params });
                        allPlayers.value = responseUsers;
                        return allPlayers.value;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching Users'
                        );
                        return [];
                }
        };

        const getAdmins = async (pageNum) => {
                let params = new URLSearchParams(); 

                params.append('page', pageNum); 

                try {
                        const responseUsers = await axios.get('/admins', { params });
                        allPlayers.value = responseUsers;
                        return allPlayers.value;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching Admins'
                        );
                        return [];
                }
        };


        const changeUserBlockedState = async (id) => {

                try {
                        const responseUsers = await axios.put('users/' + id + '/changeblock');
                        return responseUsers;
                } catch (e) {
                        storeError.setErrorMessages(
                                e.response.data.message,
                                e.response.data.errors,
                                e.response.status,
                                'Error fetching User State'
                        );
                        return [];
                }
        }

        return { allPlayers, getPlayers, getAdmins, changeUserBlockedState};
})
