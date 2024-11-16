import { createRouter, createWebHistory } from "vue-rounter"

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home"
        }
    ]
})
