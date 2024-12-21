<script setup>
import { onMounted } from 'vue';
import { useMemoryGameStore } from '@/stores/memoryGame';
import Board from './Board.vue';
import { onBeforeRouteLeave } from 'vue-router';

const memoryGameStore = useMemoryGameStore();

const play = (index) => {
    memoryGameStore.onCardClick(index)
}

const resetGame = () => {
    memoryGameStore.createGame(memoryGameStore.boardId)
}

onMounted(() => {
    if (memoryGameStore.gameFinished)
        memoryGameStore.createGame(memoryGameStore.boardId)
})

onBeforeRouteLeave ((to, from, next) => {
  if(memoryGameStore.gameFinished){
    next()
    return
  }
  
  const answer = window.confirm('Do you really want to leave? You will lose the game!')
  if (answer) {
    memoryGameStore.quitGame()
    next()
    return
  }

  next(false)
});

</script>

<template>
    <div id="memory-game" class="flex flex-col h-screen items-center justify-center relative">
        <!-- End screen display -->
        <div v-if="memoryGameStore.gameFinished"
            class="flex flex-col h-fit items-center text-white bg-[#1D4ED8] p-8 rounded-lg shadow-xl space-y-6">
            <p class="text-lg font-semibold">
                Turns taken:
                <span class="font-bold">{{ memoryGameStore.turnsTaken }}</span>
            </p>
            <p class="text-lg font-semibold">
                Time spent:
                <span class="font-bold">{{ memoryGameStore.formattedTime }}</span>
            </p>
            <button @click="resetGame"
                class="px-6 py-2 bg-[#10B981] text-white rounded-lg shadow-lg hover:bg-[#059669] transition-all">Retry</button>
        </div>

        <!-- Game in progress -->
        <div v-else class="flex flex-col w-full h-full justify-center items-center">
            <!-- Flex container for stats and tips, centered for Turns and Time cards -->
            <div class="flex justify-center items-center space-x-6 mb-6 text-lg w-full max-w-lg">
                <!-- Turns Card -->
                <div
                    class="p-4 bg-white rounded-lg shadow-lg w-1/3 text-center transform transition-transform hover:scale-105">
                    <p class="font-semibold text-primary">Turns</p>
                    <p class="text-xl font-bold">{{ memoryGameStore.turnsTaken }}</p>
                </div>

                <!-- Time Card -->
                <div
                    class="p-4 bg-white rounded-lg shadow-lg w-1/3 text-center transform transition-transform hover:scale-105">
                    <p class="font-semibold text-primary">Time</p>
                    <p class="text-xl font-bold">{{ memoryGameStore.formattedTime }}</p>
                </div>
            </div>

            <!-- Tips Button on the right side -->
            <!-- <div class="flex justify-center w-full mb-6">
                <button @click="showTipsPopup" class="px-6 py-3 text-xl bg-primary text-white rounded-lg shadow-md hover:bg-primary-light transition-all">
                    Tips
                </button>
            </div> -->

            <!-- Game Board -->

            <div class="flex max-w-3xl w-full h-full justify-center items-center pb-4">
                <Board :board="memoryGameStore.cards" :size="memoryGameStore.size" @play="play" />
            </div>
        </div>
    </div>
</template>

<style scoped>
html,
body {
    height: 100%;
    margin: 0;
}

#memory-game {
    background: linear-gradient(135deg, #f43f5e, #9333ea);
    /* Red to Purple */
}

.card-container {
    perspective: 1000px;
    /* Adds perspective to the scene */
}

.card {
    width: 100px;
    height: 180px;
    position: relative;
    transform-style: preserve-3d;
    /* Ensures the front and back are in 3D space */
    transition: transform 0.6s ease;
    /* Smooth flip transition */
}

.card.flipped {
    transform: rotateY(180deg);
    /* Flip the card 180 degrees */
}

.card-side {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    /* Prevents the back from showing when flipped */
    object-fit: cover;
}

.card .front {
    transform: rotateY(0deg);
    /* Front side faces forward */
}

.card .back {
    transform: rotateY(180deg);
    /* Back side is initially hidden */
}
</style>
