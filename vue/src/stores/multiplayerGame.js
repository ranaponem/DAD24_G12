import { defineStore } from "pinia"
import { useAuthStore } from "./auth"
import { useErrorStore } from "./error"
import { useToast } from "@/components/ui/toast"
import { inject, ref } from "vue"
import axios from "axios"


export const useMultiplayerStore = defineStore('multiplayer', () => {
    const storeAuth = useAuthStore()
    const storeError = useErrorStore()

    const { toast } = useToast()

    const socket = inject('socket')
    const game = ref(null)

    const gameInProgress = ref(false)

    const updateGame = (updatedGame) => {
        game.value = updatedGame
        if (game.value.flippedCards.length == 2) {
            game.value.flippedCards = []
        }
    }

    const playerNumberOfCurrentUser = () => game.value.player1.id == storeAuth.user.id ?
        1
        : game.value.player2.id == storeAuth.user.id ?
            2
            : null

    const webSocketServerResponseHasError = (response) => {
        if (response.errorCode) {
            storeError.setErrorMessages(response.errorMessage, [], response.errorCode)
            return true
        }
        return false
    }

    const play = (index) => {
        storeError.resetMessages()

        socket.emit(
            'play',
            {
                index: index,
                gameId: game.value.id
            },
            (response) => {
                if (webSocketServerResponseHasError(response)) {
                    return
                }
            }
        )
    }

    const quit = () => {
        storeError.resetMessages()

        socket.emit('quitGame', game.value.id, (response) => {
            if (webSocketServerResponseHasError(response)) {
                return
            }
            gameInProgress.value = false
        })
    }

    const close = () => {
        storeError.resetMessages()

        socket.emit('closeGame', game.value.id, (response) => {
            if (webSocketServerResponseHasError(response)) {
                return
            }
            gameInProgress.value = false
        })
    }

    const getFirstLastName = (player) => {
        const names = player.name.trim().split(' ')
        const firstName = names[0] ?? ''
        const lastName = names.length > 1 ? names[names.length - 1] : ''
        return (firstName + ' ' + lastName).trim()
    }

    socket.on('gameStarted', async (updatedGame) => {
        gameInProgress.value = true
        game.value = updatedGame
        storeAuth.user.brain_coins_balance -= 5
    })

    socket.on('gameEnded', async (updatedGame) => {
        updateGame(updatedGame)

        const currentUser = playerNumberOfCurrentUser()
        if (currentUser === 1 || currentUser == 2) {
            gameInProgress.value = false
            
            await axios.patch('multiplayer/' + game.value.id, {
                total_turns_winner: game.value.turns[currentUser],
                status: game.value.status,
                pairs: game.value.pairsDiscovered[currentUser],
                my_win: game.value.gameStatus === currentUser ? 1 : 0
            })
            
            if (currentUser == game.gameStatus) {
                storeAuth.user.brain_coins_balance += 7
            }
        }
    })

    socket.on('gameChanged', (updatedGame) => {
        updateGame(updatedGame)
    })

    socket.on('gameQuitted', async (quitData) => {
        if (quitData.userQuit.id != storeAuth.user.id) {
            toast({
                title: 'Game Quit',
                description: `${quitData.userQuit.name} has quitted the game, giving you the win!`
            })
        }
        updateGame(quitData.game)

        gameInProgress.value = false

        await axios.patch('multiplayer/' + quitData.game.id, {
            status: 'I',
            my_win: 1,
            other_player_id: quitData.game.player1_id,
            pairs: quitData.game.pairsDiscovered[playerNumberOfCurrentUser]
        })
        
        storeAuth.user.brain_coins_balance += 7
    })

    socket.on('gameInterrupted', async (updatedGame) => {
        updateGame(updatedGame)
        toast({
            title: 'Game Interruption',
            description: `Game was interrupted because your opponent has gone offline! You win by default`,
            variant: 'destructive'
        })
        gameInProgress.value = false

        await axios.patch('multiplayer/' + updatedGame.id, {
            status: 'I',
            my_win: 1,
            other_player_id: updatedGame.player1_id,
            pairs: updatedGame.pairsDiscovered[playerNumberOfCurrentUser]
        })
        
        storeAuth.user.brain_coins_balance += 7
    })

    return {
        game,
        gameInProgress,
        getFirstLastName,
        playerNumberOfCurrentUser,
        play,
        quit,
        close
    }
})