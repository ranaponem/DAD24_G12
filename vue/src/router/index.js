import DashboardComponent from '@/components/DashboardComponent.vue'
import LoginPage from '@/components/LoginPage.vue'
import ProfilePage from '@/components/profile/ProfilePage.vue'
import CoinsPage from '@/components/coins/CoinsPage.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import RegisterPage from '@/components/RegisterPage.vue'

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
      path: '/navbar',
      children: [

      ]
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfilePage
    },
    {
      path: '/coins',
      name: 'coins',
      component: CoinsPage
    }
  ]
})

router.beforeEach(async(to, from, next) => {
  const storeAuth = useAuthStore()
  const anonymous = ['home', 'login', 'register']

  if(anonymous.includes(to.name) || storeAuth.user)
    next()

  else
    next({name : 'login'})
}) 

export default router
