import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'
import { useAuthStore } from '@/stores/auth'

export const useLobbyStore = defineStore('lobby', () => {
  const storeAuth = useAuthStore()
  const storeError = useErrorStore()
  const socket = inject('socket')
  const games = ref([])
  const myGame = ref(null)
  const waitingPlayers = ref(false)

  const webSocketServerResponseHasError = (response) => {
    if (response.errorCode) {
      storeError.setErrorMessages(response.errorMessage, [], response.errorCode)
      return true
    }
    return false
  }

  const totalGames = computed(() => games.value?.length)

  // when the lobby changes on the server, it is updated on the client
  socket.on('lobbyChanged', (lobbyGames) => {
    games.value = lobbyGames
  })

  const tryLogin = () => {
    if(socket.emit('amILoggedIn'))
      socket.emit('login', storeAuth.user)
  }

  // fetch lobby games from the Websocket server
  const fetchGames = async () => {
    storeError.resetMessages()

    tryLogin()
     await socket.emit('fetchGames', (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      games.value = response
    })
  }

  // add a game to the lobby
  const addGame = (board) => {
    storeError.resetMessages()

    tryLogin()

    socket.emit('addGame', board, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      myGame.value = response
      waitingPlayers.value = true
      return
    })
  }

  // remove a game from the lobby
  const removeGame = () => {
    storeError.resetMessages()

    tryLogin()
    socket.emit('removeGame', myGame.value.id, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      waitingPlayers.value = false
    })
  }

  // Whether the current user can remove a specific game from the lobby
  const canRemoveGame = (game) => game.player1.id === storeAuth.user.id

  // join a game of the lobby
  const joinGame = (id, board) => {
    storeError.resetMessages()

    tryLogin()
    socket.emit('joinGame', { id: id, board: board}, async (response) => {
      // callback executed after the join is complete
      if (webSocketServerResponseHasError(response)) {
        return
      }
      waitingPlayers.value = false

      const APIresponse = await axios.post('multiplayer', {
        board_id: response.board.id,
        player_1_id: response.player1.id
      })

      const newGameOnDB = APIresponse.data.data
      newGameOnDB.player1SocketId = response.player1SocketId
      newGameOnDB.player2SocketId = response.player2SocketId
      newGameOnDB.player1 = response.player1
      newGameOnDB.player2 = response.player2

      // After adding game to the DB emit a message to the server to start the game
      socket.emit('startGame', newGameOnDB)
    })
  }

  // Whether the current user can join a specific game from the lobby
  const canJoinGame = (game) => storeAuth.user && 
    game.player1.id !== storeAuth.user.id &&
    storeAuth.user.brain_coins_balance >= 5

  const canMakeGame = computed(() => storeAuth.user && storeAuth.user.brain_coins_balance >= 5)

  return {
    games,
    myGame,
    totalGames,
    waitingPlayers,
    fetchGames,
    addGame,
    joinGame,
    canJoinGame,
    canMakeGame,
    removeGame,
    canRemoveGame
  }
})
