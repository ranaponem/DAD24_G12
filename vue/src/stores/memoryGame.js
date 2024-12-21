import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useAuthStore } from './auth';
import { useErrorStore } from './error';
import axios from 'axios';

export const useMemoryGameStore = defineStore('memory', () => {
    const storeAuth = useAuthStore()
    const storeError = useErrorStore()
    const deck = Array.from({ length: 40 }, (_, i) => i + 1)

    const cards = ref([]);
    const rows = ref(3);
    const columns = ref(4);
    const gameFinished = ref(true);
    const turnsTaken = ref(0);
    const elapsedTime = ref(0);
    const timer = ref(null);
    const game = ref({});

    const flippedCards = ref([]);
    const canFlip = ref(true);

    const formattedTime = computed(() => {
        const minutes = Math.floor(elapsedTime.value / 60);
        const seconds = elapsedTime.value % 60;
        return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    });

    const size = computed(() => rows.value * columns.value) 
    const boardId = computed(() => game.value?.board?.id ?? 1)

    const resetGame = (rowCount, columnCount) => {
        if ((rowCount * columnCount) % 2 !== 0) {
            throw new Error(
                `The size (rows * columns) of the board must be an even number. Size ${rowCount * columnCount} (${rowCount} x ${columnCount}) given.`
            )
        }

        rows.value = rowCount
        columns.value = columnCount
        turnsTaken.value = 0
        elapsedTime.value = 0
        gameFinished.value = false
        stopTimer()
        startTimer()

        cards.value = generateBoard()
    }

    const generateBoard = () => {
        let cards = [];
        const totalCards = columns.value * rows.value
        for (let i = 1; i <= (totalCards) / 2; i++) {
            cards.push(i, i);
        }

        cards = deck.sort(() => Math.random() - 0.5).slice(0, totalCards / 2);
        cards = cards.concat(cards)
        cards = cards.sort(() => Math.random() - 0.5)
            .map((cardCode, index) => ({
                id: index,
                value: cardCode,
                isFlipped: false,
                isMatched: false,
            }));

        return cards
    }


    const startTimer = () => {
        timer.value = setInterval(() => {
            elapsedTime.value++
        }, 1000)
    }

    const stopTimer = () => {
        if (timer.value)
            clearInterval(timer.value);
        timer.value = null
    }

    const createGame = async (boardId) => {
        if (storeAuth.user) {
            try {
                const response = await axios.post('/games', { board_id: boardId })
                if (boardId != 1)
                    storeAuth.user.brain_coins_balance--

                game.value = response.data.data;
            } catch (e) {
                storeError.setErrorMessages(
                    e.response.data.message,
                    e.response.data.errors,
                    e.response.status,
                    'Game Error!'
                );
                return false;
            }
        }
        resetGame(rows.value, columns.value)
        return true
    }

    const onCardClick = (index) => {
        const card = cards.value[index];

        if (card.isFlipped || card.isMatched || !canFlip.value) {
            return
        }

        card.isFlipped = true
        flippedCards.value.push(card)

        if (flippedCards.value.length == 2) {
            canFlip.value = false;
            setTimeout(() => {
                checkMatch()
            }, 500)
        }
    }


    const checkMatch = () => {
        const [first, second] = flippedCards.value;

        if (first.value === second.value) {
            first.isMatched = true;
            second.isMatched = true;

            if (checkGameOver.value) {
                endGame();
            }
        } else {
            setTimeout(() => {
                first.isFlipped = false;
                second.isFlipped = false;
            }, 500);
        }

        flippedCards.value = [];
        turnsTaken.value++;
        canFlip.value = true;
    };


    const checkGameOver = computed(() => cards.value.every(card => card.isMatched == true))

    const endGame = async () => {
        stopTimer();
        gameFinished.value = true;

        if (storeAuth.user) {
            try {
                const response = await axios.patch(`/games/${game.value.id}`, {
                    status: 'E',
                    total_turns_winner: turnsTaken.value,
                });
                game.value = response.data.data;
            } catch (e) {
                storeError.setErrorMessages(
                    e.response.data.message,
                    e.response.data.errors,
                    e.response.status,
                    'Game Error!'
                );
                return false;
            }
        }
    };

    const quitGame = async () => {
        stopTimer()
        gameFinished.value = true

        if (storeAuth.user) {
            try {
                const response = await axios.patch(`/games/${game.value.id}`, {
                    status: 'I',
                });
                game.value = response.data.data;
            } catch (e) {
                storeError.setErrorMessages(
                    e.response.data.message,
                    e.response.data.errors,
                    e.response.status,
                    'Game Error!'
                );
                return false;
            }
        }
    }

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

    return {
        cards,
        rows,
        columns,
        size,
        boardId,
        gameFinished,
        turnsTaken,
        elapsedTime,
        formattedTime,
        resetGame,
        quitGame,
        createGame,
        startTimer,
        stopTimer,
        onCardClick,
        checkMatch,
        checkGameOver,
        endGame,
        cardHint
    };
});
