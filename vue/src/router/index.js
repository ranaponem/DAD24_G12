import DashboardComponent from '@/components/DashboardComponent.vue'
import LoginPage from '@/components/LoginPage.vue'
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
      path: '/login',
      name: 'login',
      component: LoginPage
    },
    {
      path: '/navbar',
      children: [
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
