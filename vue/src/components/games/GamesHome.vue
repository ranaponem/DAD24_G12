<script setup>
import { useAuthStore } from '@/stores/auth';
import { useBoardStore } from '@/stores/board';
import { useGameStore } from '@/stores/game';
import { onMounted } from 'vue'


const storeBoard = useBoardStore()
const storeAuth = useAuthStore()
const storeGame = useGameStore()

onMounted(() => {
    storeBoard.fetchBoards()
})

</script>

<template>
    <div class="flex flex-col items-center text-center space-y-8 py-12">
        <div class="max-w-2xl mx-auto px-4 space-y-4">
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Single Player
            </h1>
            <div class="space-y-2">
                <button
                    v-show="storeAuth.user"
                    v-for="board in storeBoard.boards"
                    :key="`${board.board_cols}x${board.board_rows}`"
                    @click.prevent="storeGame.createSingleplayerGame(board.id)"
                    :to="`/game/${storeGame.game.id}`"
                    class="w-full px-4 py-2 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light block text-center">
                    {{ `${board.board_cols}x${board.board_rows}` }}
                </button>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Multi Player
            </h1>
            <div class="space-y-2">
                <RouterLink
                    v-for="board in storeBoard.boards"
                    :key="`${board.board_cols}x${board.board_rows}`"
                    @click = storeGame.createMultiplayerGame(board.id)
                    class="w-full px-4 py-2 text-white bg-primary hover:bg-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-tertiary-light block text-center"
                >
                    {{ `${board.board_cols}x${board.board_rows}` }}
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  methods: {
    handleClick(board) {
      this.createSingleplayerGame(board);
      this.functionTwo();
    },
    createSingleplayerGame(board) {
        storeBoard.createSingleplayerGame(board.id);
    },
    functionTwo() {
      console.log("Function Two");
    },
  },
};
</script>