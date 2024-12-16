import DashboardComponent from '@/components/DashboardComponent.vue'
import LaravelTester from '@/components/LaravelTester.vue'
import GamesHome from '@/components/games/GamesHome.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import MemoryGame from '@/components/games/MemoryGame.vue'
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
          component: LaravelTester
        },
        {
          path: 'websocket',
          component: WebSocketTester
        }
      ]
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

export default router
