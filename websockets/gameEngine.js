export function createGameEngine() {
    const deck = Array.from({ length: 40 }, (_, i) => i + 1)

    const initGame = (gameFromDB) => {
        gameFromDB.gameStatus = 0; //
        // 0 -> game has started and running
        // 1 -> player 1 is the winner
        // 2 -> player 2 is the winner
        gameFromDB.currentPlayer = 1 // Player 1 starts
        gameFromDB.pairsDiscovered = { 1: 0, 2: 0 }
        gameFromDB.turns = { 1: 0, 2: 0 }
        gameFromDB.size = gameFromDB.board.board_cols * gameFromDB.board.board_rows;
        gameFromDB.board = generateBoard(gameFromDB.board)
        gameFromDB.matchedPairs = []
        gameFromDB.flippedCards = []

        return gameFromDB
    }

    const generateBoard = (board) => {
        let cards = [];
        const totalCards = board.board_cols * board.board_rows
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

        return cards;
    };

    const pairsDiscoveredFor = (game, player_id) => {
        game.pairsDiscovered[player_id] += 1;
    };

    // returns whether the game as ended or not
    const gameEnded = (game) => game.gameStatus !== 0;

    // Plays a specific piece of the game (defined by its index)
    // Returns true if the game play is valid or an object with an error code and message otherwise;
    const play = (game, index, playerSocketId, roomName, io) => {
        if (
            playerSocketId != game.player1SocketId &&
            playerSocketId != game.player2SocketId
        ) {
            return {
                errorCode: 403,
                errorMessage: "You are not playing this game!",
            };
        }
        if (gameEnded(game)) {
            return {
                errorCode: 400,
                errorMessage: "Game has already ended!",
            };
        }
        if (
            (game.currentPlayer == 1 &&
                playerSocketId != game.player1SocketId) ||
            (game.currentPlayer == 2 && playerSocketId != game.player2SocketId)
        ) {
            return {
                errorCode: 400,
                errorMessage: "Invalid play: It is not your turn!",
            };
        }
        const card = game.board[index];

        if (card.isFlipped || card.isMatched) {
            return {
                errorCode: 400,
                errorMessage: "Invalid move: card already flipped or matched!",
            };
        }

        // Flip the card
        card.isFlipped = true;
        game.flippedCards.push(card);

        io.to(roomName).emit("gameChanged", game);

        // game.lastAction[game.currentPlayer] = game.stopwatch.
        if (game.flippedCards.length === 2) {
            game.turns[game.currentPlayer] += 1;

            const [firstCard, secondCard] = game.flippedCards;

            if (firstCard.value === secondCard.value) {
                firstCard.isMatched = true;
                secondCard.isMatched = true;

                game.matchedPairs.push(firstCard.value);
                game.flippedCards = [];
                pairsDiscoveredFor(game, game.currentPlayer);
                changeGameStatus(game);

                io.to(roomName).emit("gameChanged", game);
            } else {
                setTimeout(() => {
                    firstCard.isFlipped = false;
                    secondCard.isFlipped = false;

                    // Clear flipped cards and change the current player
                    game.flippedCards = [];
                    game.currentPlayer = game.currentPlayer === 1 ? 2 : 1;

                    io.to(roomName).emit("gameChanged", game);
                }, 1000);
            }
        }

        if (gameEnded(game)) {

            io.to(roomName).emit("gameEnded", game);
        }

        return true;
    };

    const changeGameStatus = (game) => {
        if (isGameComplete(game)) {
            if (game.pairsDiscovered[1] === game.pairsDiscovered[2]) {
                if (game.currentPlayer == 1) {
                    game.gameStatus = 2;
                } else {
                    game.gameStatus = 1;
                }
            } else {
                game.gameStatus =
                    game.pairsDiscovered[1] > game.pairsDiscovered[2] ? 1 : 2;
            }
            game.status = "E";
        }
    };

    const isGameComplete = (game) => {
        return game.matchedPairs.length === game.size / 2;
    };

    // One of the players quits the game. The other one wins the game
    const quit = (game, playerSocketId) => {
        if (
            playerSocketId != game.player1SocketId &&
            playerSocketId != game.player2SocketId
        ) {
            return {
                errorCode: 403,
                errorMessage: "You are not playing this game!",
            };
        }
        if (gameEnded(game)) {
            return {
                errorCode: 400,
                errorMessage: "Game has already ended!",
            };
        }

        game.gameStatus = playerSocketId == game.player1SocketId ? 2 : 1;
        game.status = "I";
        return true;
    };
    // Check if socket can close the game (game must have ended and player must belong to game)
    const close = (game, playerSocketId) => {
        if (
            playerSocketId != game.player1SocketId &&
            playerSocketId != game.player2SocketId
        ) {
            return {
                errorCode: 403,
                errorMessage: "You are not playing this game!",
            };
        }
        if (!gameEnded(game)) {
            return {
                errorCode: 400,
                errorMessage: "Cannot close a game that has not ended!",
            };
        }
        return true;
    };

    return {
        initGame,
        gameEnded,
        play,
        quit,
        close,
    };
}
