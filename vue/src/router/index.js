import DashboardComponent from '@/components/DashboardComponent.vue'
import MemoryGame from '@/components/games/MemoryGame.vue'
import GamesHome from '@/components/games/GamesHome.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import RegisterPage from '@/components/RegisterPage.vue'
import CreateAdminPage from '@/components/CreateAdminPage.vue'

const router = createRouter({
        history: createWebHistory(import.meta.env.BASE_URL),
        routes: [
                {
                        path: '/',
                        name: 'home',
                        component: DashboardComponent
                },
                {
                        path: '/login',
                        name: 'login',
                        component: LoginPage
                },
                {
                        path: '/register',
                        name: 'register',
                        component: RegisterPage
                },
                {
                        path: '/createadmin',
                        name: 'createadmin',
                        component: CreateAdminPage
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
                },
                {
                        path: '/transactions',
                        name: 'transactions',
                        component: TransactionsComponent
                },
                {
                        path: '/statistics',
                        name: 'statistics',
                        component: StatisticsComponent
                },
                {
                        path: '/administration',
                        name: 'administration',
                        component: AdministrationComponent
                },
                {
                  path: '/games',
                  name: 'games',
                  component: GamesHome
                },
                {
                  path: '/games/play',
                  name: 'MemoryGame',
                  props: route => ({
                    rows: parseInt(route.params.rows),
                    cols: parseInt(route.params.cols),
                  }),
                  component: MemoryGame,
                }
        ]
})
router.beforeEach(async (to, from, next) => {
        const storeAuth = useAuthStore()
        const anonymous = ['home', 'login', 'scoreboard', 'register']

        if (anonymous.includes(to.name) || storeAuth.user)
        next()

        else {
                if (confirm('You must be logged in to access this page!')) {
                        next({ name: 'login' })
                }
        }
})
export default router
