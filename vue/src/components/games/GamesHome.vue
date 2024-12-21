<script setup>
import { useAuthStore } from '@/stores/auth';
import { useBoardStore } from '@/stores/board';
import { useMemoryGameStore } from '@/stores/memoryGame';
import { onMounted } from 'vue'
import { useRouter } from 'vue-router';

const router = useRouter()
const storeBoard = useBoardStore()
const storeAuth = useAuthStore()
const storeMemoryGame = useMemoryGameStore()

const startGame = async (cols, rows, id) => {
    storeMemoryGame.rows = rows
    storeMemoryGame.columns = cols
    const result = await storeMemoryGame.createGame(id)
    if (result)
        router.push({ name: 'MemoryGame' })
}

onMounted(() => {
    storeBoard.fetchBoards()
})

</script>

<template>
    <div class="flex flex-col w-full self-center justify-center text-center space-y-8 py-12">
        <div class="max-w-2xl mx-auto px-4">

            <!-- Single Player Section -->
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-5">
                Singleplayer games
            </h1>
            <div class="space-y-2">
                <button v-for="(board, index) in storeBoard.boards" :key="`${board.board_cols}x${board.board_rows}`"
                    :disabled="!storeAuth.user && index !== 0"
                    @click.prevent="startGame(board.board_cols, board.board_rows, board.id)"
                    class="w-full px-4 py-2 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light block text-center"
                    :class="{ 'opacity-50 cursor-not-allowed': !storeAuth.user && index !== 0 }">
                    {{ `${board.board_cols}x${board.board_rows}` }}
                </button>
            </div>
            <div class="my-16"></div>
            <!-- Multi Player Section -->
            <h1 v-show="storeAuth.user" class="text-3xl font-bold text-gray-900 sm:text-4xl mb-5">
                Go to multiPlayer lobby
            </h1>
            <RouterLink to="/games/lobby" v-show="storeAuth.user">
                <div
                    class="w-full px-4 py-2 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light block text-center">
                    Join the lobby
                </div>
            </RouterLink>
        </div>
    </div>
</template>
