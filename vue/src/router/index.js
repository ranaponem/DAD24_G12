import DashboardComponent from '@/components/DashboardComponent.vue'
import LaravelTester from '@/components/LaravelTester.vue'
import ProfilePage from '@/components/profile/ProfilePage.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import { createRouter, createWebHistory } from 'vue-router'

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
    }
  ]
})

export default router
