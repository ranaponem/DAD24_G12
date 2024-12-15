import DashboardComponent from '@/components/DashboardComponent.vue'
import HistoryComponent from '@/components/HistoryComponent.vue'
import ScoreboardComponent from '@/components/ScoreboardComponent.vue'
import LaravelTester from '@/components/LaravelTester.vue'
import ProfilePage from '@/components/profile/ProfilePage.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
        history: createWebHistory(import.meta.env.BASE_URL),
        routes: [
                {
                        path: '/',
                        name: 'home',
                        component: DashboardComponent
                },
                {
                        path: '/testers',
                        children: [
                                {
                                        path: 'laravel',
                                        name: 'login',
                                        component: LaravelTester
                                },
                                {
                                        path: 'websocket',
                                        component: WebSocketTester
                                }
                        ]
                },
                {
                        path: '/profile',
                        name: 'profile',
                        component: ProfilePage
                },
                {
                        path: '/history',
                        name: 'history',
                        component: HistoryComponent,
                },
                {
                        path: '/scoreboard',
                        name: 'scoreboard',
                        component: ScoreboardComponent
                }
        ]
})
router.beforeEach(async(to, from, next) => {
        const storeAuth = useAuthStore()
        const anonymous = ['home', 'login', 'scoreboard']

        if(anonymous.includes(to.name) || storeAuth.user)
        next()

        else{
                if(confirm('You must be logged in to access this page!')){
                        next({name : 'login'})
                }
        }
}) 
export default router
