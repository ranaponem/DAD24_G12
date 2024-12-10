import { ref } from 'vue'
import { useBoardStore } from './board'

export default {
    setup() {
        // Store references
        const storeBoard = useBoardStore()

        // Reactive variables
        const memoryGames = ref([])
        const cardArray = ref([])
        const cardsWithImages = ref([])

        // Function to create a basic card array
        const createCardsArray = () => {
            cardArray.value = []

            const cols = storeBoard.board_cols
            const rows = storeBoard.board_rows

            if (cols && rows) {
                const totalCards = cols * rows
                cardArray.value = Array.from({ length: totalCards }, (_, index) => index + 1)
            }
        }

        // Function to load cards with images
        const loadCards = () => {
            // Ensure board dimensions are valid
            const cols = storeBoard.board_cols
            const rows = storeBoard.board_rows
            if (!cols || !rows || (cols * rows) % 2 !== 0) {
                console.warn('Invalid board dimensions. Ensure total cards are an even number.')
                return
            }

            // Initialize an empty array for cards
            cardsWithImages.value = []

            const totalCards = cols * rows
            const usedImages = new Set()

            // Function to get a random card image index from 1 to 54
            const getRandomCardImage = () => {
                let randomImage
                do {
                    randomImage = Math.floor(Math.random() * 54) + 1
                } while (usedImages.has(randomImage))
                usedImages.add(randomImage)
                return randomImage
            }

            // Generate pairs of cards with random images
            for (let i = 0; i < totalCards / 2; i++) {
                const randomImage = getRandomCardImage()

                // Push the same image twice for pairing
                cardsWithImages.value.push(
                    { id: cardsWithImages.value.length + 1, image: new URL(`../assets/cards/card_${randomImage}.png`, import.meta.url).href },
                    { id: cardsWithImages.value.length + 2, image: new URL(`../assets/cards/card_${randomImage}.png`, import.meta.url).href }
                )
            }

            // Shuffle the cards to randomize positions
            cardsWithImages.value = cardsWithImages.value.sort(() => Math.random() - 0.5)
        }

        // Return reactive properties and functions
        return {
            memoryGames,
            cardArray,
            cardsWithImages,
            createCardsArray,
            loadCards,
        }
    },
}
