<script setup>
import { ref, watch } from 'vue'
import { computed } from 'vue'
import Board from './Board.vue'
import { useMultiplayerStore } from '@/stores/multiplayerGame'
import { useAuthStore } from '@/stores/auth';
import { onBeforeRouteLeave, useRouter } from 'vue-router';

const storeMG = useMultiplayerStore()
const storeAuth = useAuthStore()
const router = useRouter()

const turnTime = ref({
  time: 20,
  clock: null
})

const match = computed(() => storeMG.game?.flippedCards.length == 2)

const gameEnded = computed(() => storeMG.game?.gameStatus > 0)

const currentUserTurn = computed(() => {
  if (gameEnded.value)
    return false

  if (storeMG.game.currentPlayer === storeMG.playerNumberOfCurrentUser()) {
    resetClock()
    return true
  }

  stopClock()
  return false
})

const resetClock = () => {
  turnTime.value.time = 20
  if(turnTime.value.clock)
    clearInterval(turnTime.value.clock)
  turnTime.value.clock = setInterval(() => {
    turnTime.value.time--;
  }, 1000) 
}

const stopClock = () => {
  clearInterval(turnTime.value.clock)
  turnTime.value.clock = null
  turnTime.value.time = 0
}

const seconds = computed(() => turnTime.value.time > -1 ? turnTime.value.time : 0)

const turns = computed(() => storeMG.game?.turns[storeMG.playerNumberOfCurrentUser()] ?? 0)

const pairs = computed(() => storeMG.game?.pairsDiscovered[storeMG.playerNumberOfCurrentUser()] ?? 0)

const statusMessageColor = computed(() => {
  switch (storeMG.game?.gameStatus) {
    case 0:
      return currentUserTurn.value ? 'text-lime-600' : 'text-gray-400'
    case 1:
    case 2:
      return storeMG.playerNumberOfCurrentUser() == storeMG.game.gameStatus
        ? 'text-lime-600'
        : 'text-red-900'
    default:
      return 'text-gray-800'
  }
})

const statusGameMessage = computed(() => {
  switch (storeMG.game?.gameStatus) {
    case 0:
      const currentPlayer = storeMG.game.currentPlayer == 1 ? storeMG.game.player1 : storeMG.game.player2
      return currentPlayer.id == storeAuth.user.id ? "Your Turn!" : currentPlayer.nickname + "'s Turn!"
    case 1:
    case 2:
      stopClock()
      return storeMG.playerNumberOfCurrentUser() == storeMG.game.gameStatus
        ? 'You won'
        : 'You lost'
    default:
      return ''
  }
})

const chooseCardOnBoard = (index) => {
  if (!gameEnded.value && currentUserTurn.value && !match.value) {
    stopClock()
    storeMG.play(index)
  }
}

const quitGame = () => {
  router.push({ name: 'lobby' })
}

watch(() => turnTime.value.time < 0, (newValue) => {
  if (newValue == true) {
    storeMG.quit()
    storeMG.game.gameStatus = storeMG.playerNumberOfCurrentUser() == 1 ? 2 : 1
  }
})

onBeforeRouteLeave ((to, from, next) => {
  if(gameEnded.value){
    storeMG.close()
    next()
    return
  }
  
  const answer = window.confirm('Do you really want to leave? You will lose the game!')
  if (answer) {
    storeMG.quit()
    next()
    return
  }

  next(false)
});

</script>

<template>
  <div class="flex flex-col flex-grow w-full h-screen justify-center items-center">
    <div class="flex flex-col lg:flex-row w-full h-full max-w-7xl justify-center items-center">
      <div class="flex flex-col max-w-lg space-y-8 w-fit mb-3 justify-center items-center mx-6">
        <button @click="quitGame" class="flex bg-red-600 p-3 rounded m-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
          {{ gameEnded ? 'Leave' : 'Quit' }}
        </button>
        <div class="flex flex-col bg-primary rounded px-12 py-3 text-center text-nowrap">
          <p class="flex flex-col text-lg sm:text-xl text-gray-800 dark:text-gray-300">
            My Moves:
            <span class="font-semibold text-black dark:text-white">{{ turns }}</span>
          </p>
          <p class="flex flex-col text-lg sm:text-xl text-gray-800 dark:text-gray-300">
            Pairs found:
            <span class="font-semibold text-black dark:text-white">{{ pairs }}</span>
          </p>
          <p class="flex flex-col text-lg sm:text-xl text-gray-800 dark:text-gray-300 justify-center items-center">
            Remaining Time:
            <span class="font-semibold text-black dark:text-white">{{ seconds }}</span>
          </p>
        </div>
      </div>
      <div class="flex flex-col w-full h-full justify-center items-center">
        <h3 class="pt-0 text-2xl font-bold py-2" :class="statusMessageColor">
          {{ statusGameMessage }}
        </h3>
        <div class="flex max-w-3xl w-full h-full justify-center items-center pb-4">
          <Board :board="storeMG.game.board" :size="storeMG.game.size" @play="chooseCardOnBoard" />
        </div>
      </div>
    </div>
  </div>
</template>
