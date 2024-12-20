<script setup>
import { computed, onMounted } from 'vue';
import { useMemoryGameStore } from '@/stores/memoryGame';

const memoryGameStore = useMemoryGameStore();

const gameBoard = computed(() => memoryGameStore.cards);

function handleCardClick(index) {
    memoryGameStore.onCardClick(index);
}

function resetGame() {
    memoryGameStore.resetGame(memoryGameStore.rows, memoryGameStore.columns);
}

function showTipsPopup() {
    // Implement your tips logic here (optional)
}

onMounted(() => {
    if (memoryGameStore.rows > 0 && memoryGameStore.columns > 0) {
        memoryGameStore.resetGame(memoryGameStore.rows, memoryGameStore.columns);
    }
});
</script>

<template>
    <div id="memory-game" class="flex flex-col items-center p-6 min-h-screen relative">
        <!-- End screen display -->
        <div v-if="memoryGameStore.gameFinished" class="flex flex-col items-center text-white bg-[#1D4ED8] p-8 rounded-lg shadow-xl space-y-6">
            <p class="text-lg font-semibold">Turns taken: <span class="font-bold">{{ memoryGameStore.turnsTaken }}</span></p>
            <p class="text-lg font-semibold">Time spent: <span class="font-bold">{{ memoryGameStore.formattedTime }}</span></p>
            <button @click="resetGame" class="px-6 py-2 bg-[#10B981] text-white rounded-lg shadow-lg hover:bg-[#059669] transition-all">Retry</button>
        </div>

        <!-- Game in progress -->
        <div v-else>
            <!-- Flex container for stats and tips, centered for Turns and Time cards -->
            <div class="flex justify-center items-center space-x-6 mb-6 text-lg w-full max-w-lg">
                <!-- Turns Card -->
                <div class="p-4 bg-white rounded-lg shadow-lg w-1/3 text-center transform transition-transform hover:scale-105">
                    <p class="font-semibold text-primary">Turns</p>
                    <p class="text-xl font-bold">{{ memoryGameStore.turnsTaken }}</p>
                </div>

                <!-- Time Card -->
                <div class="p-4 bg-white rounded-lg shadow-lg w-1/3 text-center transform transition-transform hover:scale-105">
                    <p class="font-semibold text-primary">Time</p>
                    <p class="text-xl font-bold">{{ memoryGameStore.formattedTime }}</p>
                </div>
            </div>

            <!-- Tips Button on the right side -->
            <div class="flex justify-center w-full mb-6">
                <button @click="showTipsPopup" class="px-6 py-3 text-xl bg-primary text-white rounded-lg shadow-md hover:bg-primary-light transition-all">
                    Tips
                </button>
            </div>

            <!-- Game Board -->
            <div
                class="grid gap-6"
                :style="{
                    gridTemplateColumns: `repeat(${memoryGameStore.columns}, minmax(100px, 1fr))`,
                    gridTemplateRows: `repeat(${memoryGameStore.rows}, minmax(180px, 1fr))`
                }"
            >
                <div
                    v-for="(card, index) in gameBoard"
                    :key="index"
                    class="relative w-[100px] h-[180px] rounded-lg overflow-hidden perspective-[1000px] cursor-pointer transform transition-transform duration-300 hover:scale-110"
                    :class="{
                        'flipped': card.flipped,
                        'matched': card.matched
                    }"
                    @click="handleCardClick(index)"
                >
                    <img v-show="card.flipped || card.matched" :src="card.image" alt="Card" class="w-full h-full object-contain transition-opacity duration-300" />
                    <img v-show="!card.flipped && !card.matched" :src="card.backImage" alt="Card Back" class="w-full h-full object-contain transition-opacity duration-300" />
                </div>
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
  background: linear-gradient(135deg, #f43f5e, #9333ea); /* Red to Purple */
  min-height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.card-container {
  perspective: 1000px; /* Adds perspective to the scene */
}

.card {
  width: 100px;
  height: 180px;
  position: relative;
  transform-style: preserve-3d; /* Ensures the front and back are in 3D space */
  transition: transform 0.6s ease; /* Smooth flip transition */
}

.card.flipped {
  transform: rotateY(180deg); /* Flip the card 180 degrees */
}

.card-side {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden; /* Prevents the back from showing when flipped */
  object-fit: cover;
}

.card .front {
  transform: rotateY(0deg); /* Front side faces forward */
}

.card .back {
  transform: rotateY(180deg); /* Back side is initially hidden */
}
</style>
