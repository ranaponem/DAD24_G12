import { ref, reactive, computed } from 'vue';
import { defineStore } from 'pinia';
import { useAuthStore } from './auth';

export const useMemoryGameStore = defineStore('memory', () => {
  const authStore = useAuthStore()

  const cards = ref([]);
  const rows = ref(0);
  const columns = ref(0);
  const gameFinished = ref(false);
  const turnsTaken = ref(0);
  const elapsedTime = ref(0);
  const timer = ref(null);
  const NO_CARD = -1;
  const game = ref([]);
  const gameType = ref(null)
  const realTime = ref({
    startTime: null,
    endTime: null
  })

  const flippedCards = ref([]);
  const canFlip = ref(true);

  const formattedTime = computed(() => {
    const minutes = Math.floor(elapsedTime.value / 60);
    const seconds = elapsedTime.value % 60;
    return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
  });

  const resetGame = (rowCount, columnCount) => {
    if ((rowCount * columnCount) % 2 !== 0) {
      throw new Error(
        `The size (rows * columns) of the board must be an even number. Size ${rowCount * columnCount} (${rowCount} x ${columnCount}) given.`
      );
    }

    rows.value = rowCount;
    columns.value = columnCount;
    turnsTaken.value = 0;
    elapsedTime.value = 0;
    gameFinished.value = false;
    clearInterval(timer.value);

    const totalCards = rowCount * columnCount;
    
    const allCardImages = Array.from({ length: 54 }, (_, i) => `/cards/card_${i + 1}.png`);
    const selectedImages = allCardImages.sort(() => Math.random() - 0.5).slice(0, totalCards / 2);

    const deck = [...selectedImages, ...selectedImages].sort(() => Math.random() - 0.5);
    cards.value = deck.map(image => ({
      image,
      flipped: false,
      matched: false,
      backImage: `/cards/card_back_image.jpg`
    }));

    startTimer();
  };

  const startTimer = () => {
    realTime.value.startTime = Date.now()
    timer.value = setInterval(() => {
      elapsedTime.value++;
    }, 1000);
  };

  const createGame = async (board,type) => {
    gameType.value = type
    if(authStore.user){
    try {
        const response = await axios.post('/games', {
            type: type,
            board_id: board.id,
        });
        game.value = response.data.data;
    } catch (e) {
        storeError.setErrorMessages(
            e.response?.data?.message || 'Failed to create singleplayer game.',
            e.response?.data?.errors || [],
            e.response?.status || 500,
            'Authentication Error!'
        );
        return false;
    }
  }
};

  const stopTimer = () => {
    clearInterval(timer.value);
  };

  const onCardClick = (index) => {
    const card = cards.value[index];
  
    if (card.flipped || card.matched || !canFlip.value) {
      return;
    }
  
    card.flipped = true;
    flippedCards.value.push({ index, card });
  
    if (flippedCards.value.length === 2) {
      canFlip.value = false;
      setTimeout(() => {
        checkMatch();
      }, 1000);
    }
  };
  

  const checkMatch = () => {
    const [first, second] = flippedCards.value;
  
    if (first.card.image === second.card.image) {
      first.card.matched = true;
      second.card.matched = true;
  
      if (checkGameOver()) {
        endGame();
      }
    } else {
      setTimeout(() => {
        first.card.flipped = false;
        second.card.flipped = false;
      }, 500);
    }
  
    flippedCards.value = [];
    turnsTaken.value++;
    canFlip.value = true;
  };
  

  const checkGameOver = () => {
    return cards.value.every(card => card.matched);
  };

  const endGame = async() => {
    realTime.value.endTime = Date.now()
    stopTimer();
    gameFinished.value = true;

    if(authStore.user){
      try {
        if(gameType.value == 'S'){
          const response = await axios.patch(`/games/${game.value.id}`, {
              created_user_id: authStore.user.id,
              winner_user_id: authStore.user.id,
              status: 'E',
              total_turns_winner: turnsTaken.value,
              total_time: realTime.value.endTime - realTime.value.startTime
          });
        }
          game.value = response.data.data;
      } catch (e) {
          storeError.setErrorMessages(
              e.response?.data?.message || 'Failed to end singleplayer game.',
              e.response?.data?.errors || [],
              e.response?.status || 500,
              'Authentication Error!'
          );
          return false;
      }
    }
  };

  const cardHint = () => {
    const cardIndices = [-1, -1];
    const cardMap = new Map();

    for (let i = 0; i < cards.value.length; i++) {
      const card = cards.value[i];
      if (!card.matched && !card.flipped) {
        if (cardMap.has(card.image)) {
          cardIndices[0] = cardMap.get(card.image);
          cardIndices[1] = i;
          break;
        } else {
          cardMap.set(card.image, i);
        }
      }
    }

    return cardIndices;
  };

  const closeTipsPopup = () => {
  };

  return {
    cards,
    rows,
    columns,
    gameFinished,
    turnsTaken,
    elapsedTime,
    formattedTime,
    NO_CARD,
    resetGame,
    createGame,
    startTimer,
    stopTimer,
    onCardClick,
    checkMatch,
    checkGameOver,
    endGame,
    cardHint,
    closeTipsPopup
  };
});
