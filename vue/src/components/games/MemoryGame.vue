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
}

onMounted(() => {
  if (memoryGameStore.rows > 0 && memoryGameStore.columns > 0) {
    memoryGameStore.resetGame(memoryGameStore.rows, memoryGameStore.columns);
  }
});
</script>

<template>
  <div id="memory-game" class="memory-game">
    <div v-if="memoryGameStore.gameFinished" class="end-screen">
      <p>Turns taken: {{ memoryGameStore.turnsTaken }}</p>
      <p>Time spent: {{ memoryGameStore.formattedTime }}</p>
      <button @click="resetGame">Retry</button>
    </div>

    <div v-else>
      <div class="game-stats">
        <p>Turns taken: {{ memoryGameStore.turnsTaken }}</p>
        <p>Time: {{ memoryGameStore.formattedTime }}</p>
        <button @click="showTipsPopup">Tips</button>
      </div>

      <div
        class="game-board"
        :style="{
          gridTemplateColumns: `repeat(${memoryGameStore.columns}, 1fr)`,
          gridTemplateRows: `repeat(${memoryGameStore.rows}, 1fr)`
        }"
      >
        <div
          v-for="(card, index) in gameBoard"
          :key="index"
          class="card"
          @click="handleCardClick(index)"
          :class="{ flipped: card.flipped, matched: card.matched }"
        >
          <img v-show="card.flipped || card.matched" :src="card.image" alt="Card" />
          <img v-show="!card.flipped && !card.matched" :src="card.backImage" alt="Card Back" />
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.memory-game {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

.game-board {
  display: grid;
  gap: 10px;
  width: 100%;
  max-width: 600px;
}

.card {
  max-width: 80px;
  max-height: 140px;
  margin: 5px;
  position: relative;
  perspective: 1000px;
  cursor: pointer;
}

.end-screen {
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>
