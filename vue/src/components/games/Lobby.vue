<script setup>
import { useBoardStore } from "@/stores/board";
import { useLobbyStore } from "@/stores/lobby";
import { onActivated, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "../ui/toast";
import { useAuthStore } from "@/stores/auth";
import { useMultiplayerStore } from "@/stores/multiplayerGame";

const { toast } = useToast();
const router = useRouter()
const storeLobby = useLobbyStore()
const storeMG = useMultiplayerStore()
const storeBoard = useBoardStore()
const storeAuth = useAuthStore()

const showCreateMenu = ref(false)
const changeShowCreateMenu = () => showCreateMenu.value = !showCreateMenu.value
const showLeaveGame = ref(false)
const message = ref('')

const leaveGame = () => {
    storeLobby.removeGame()
}

const createBoard = (board) => {
    if(storeLobby.canMakeGame){
        storeLobby.addGame(board)
    } else {
        toast({
            title: 'Not enough Balance',
            description: 'You do not have enough brain coins to play multiplayer games.',
            variant: 'destructive'
        })
    }
}

const joinBoard = (game) => {
    if (storeLobby.canJoinGame(game)) {
        storeLobby.joinGame(game.id, game.board)
    } else {
        toast({
            title: 'Not enough Balance',
            description: 'You do not have enough brain coins to play multiplayer games.',
            variant: 'destructive'
        })
    }
}

watch (
    () => storeLobby.waitingPlayers,
    (newValue) => {
        if(newValue) {
            showLeaveGame.value = true
            showCreateMenu.value = false
            message.value = 'Waiting for another player to join'
        }
        else{
            showLeaveGame.value = false
            showCreateMenu.value = true
            message.value = ''
        }
    }
)

watch (
    () => storeMG.gameInProgress,
    (newValue) => {
        if(newValue) {
            storeLobby.waitingPlayers = false
            router.push({ name: 'mpgame' })
        }
    }
)

onMounted(() => {
    storeBoard.fetchBoards()
    storeLobby.fetchGames()
})

onActivated(() => {
    storeLobby.fetchGames()
})

</script>

<template>
    <div class="flex flex-col lg:flex-row w-full h-full p-6 justify-center">
        <div class="relative h-fit w-fit my-5 self-center lg:self-start">
            <button v-show="showLeaveGame" @click.prevent="leaveGame"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold w-40 py-3 px-5 rounded shadow">
                Leave Game
            </button>
            <button v-show="!showLeaveGame" @click.prevent="changeShowCreateMenu"
                class="bg-secondary-light dark:bg-secondary-dark hover:bg-secondary text-white font-semibold w-40 py-3 px-5 rounded shadow">
                Create Game
            </button>
            <div v-show="showCreateMenu"
                class="flex flex-col bg-gray-100 dark:bg-gray-800 relative lg:absolute w-40 py-2 rounded shadow items-center justify-center">
                <span class="w-full text-nowrap text-center border-b-2 border-gray-400">Choose a Board:</span>
                <hr class="border-1 border-gray-600">
                <button v-for="(board) in storeBoard.boards" :key="board" class="w-full border-b-2 border-gray-400 py-2"
                    @click.prevent="createBoard(board)">
                    {{ board.board_cols + 'x' + board.board_rows }}
                </button>
            </div>
        </div>
        <div class="flex flex-col bg-gray-100 dark:bg-gray-800 w-full max-w-4xl mx-4 shadow rounded-lg self-center">
            <div class="text-center rounded">{{ message }}</div>
            <h1 class="w-full bg-primary text-white py-4 px-6 rounded-t-lg font-bold text-center text-3xl">Game Lobby
            </h1>
            <table v-show="storeLobby.totalGames > 0" class="divide-y bg-gray-300 divide-gray-400">
                <thead class="">
                    <th class="w-1/2">Player</th>
                    <th class="w-1/2">Board</th>
                    <th class="w-auto"></th>
                </thead>
                <tbody class="bg-gray-100">
                    <tr v-for="(game, index) in storeLobby.games" :key="index"
                        class="w-full h-12 justify-between hover:bg-gray-50">
                        <td class="w-1/2 text-sm text-gray-600 text-center">
                            {{ game.player1.nickname }}
                        </td>
                        <td colspan="3" class="w-1/2 text-sm text-gray-600 text-center">
                            {{ game.board.board_cols + 'x' + game.board.board_rows }}
                        </td>
                        <td class="w-auto h-12 flex justify-center items-center">
                            <button @click.prevent="joinBoard(game)" v-show="!showLeaveGame"
                                class="bg-primary-light h-fit flex text-gray-200 px-4 mr-2 py-1 rounded-lg hover:bg-primary">
                                Join
                            </button>
                            <button @click.prevent="leaveGame(game)" v-show="showLeaveGame && storeAuth.user.id == game.player1.id"
                                class="bg-red-600 hover:bg-red-700 h-fit flex text-gray-200 px-4 mr-2 py-1 rounded-lg hover:bg-primary">
                                Leave
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-show="storeLobby.totalGames == 0" class="text-black dark:text-white text-center text-xl py-4">
                No games for now
            </div>
        </div>
    </div>
</template>