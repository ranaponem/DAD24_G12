const { createGameEngine } = require("./gameEngine");
const { createLobby } = require("./lobby");
const { createUtil } = require("./util");

const gameEngine = createGameEngine();
const lobby = createLobby();
const util = createUtil();
const httpServer = require("http").createServer();

const io = require("socket.io")(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true,
    },
});

httpServer.listen(8080, () => {
    console.log("listening on *:8080");
});

io.on("connection", (socket) => {

    socket.on("login", (user) => {
        // Stores user information on the socket as "user" property
        socket.data.user = user;
        if (user && user.id) {
            socket.join("user_" + user.id);
            socket.join("lobby");
        }
    });

    socket.on("amILoggedIn", () => {
        return socket.data.user && socket.data.user != undefined
    })

    socket.on("logout", (user) => {
        if (user && user.id) {
            socket.leave("user_" + user.id);

            lobby.leaveLobby(socket.id);
            io.to("lobby").emit("lobbyChanged", lobby.getGames());
            socket.leave("lobby");
            
            util.getRoomGamesPlaying(socket).forEach(([roomName, room]) => {
                socket.leave(roomName);
                if (!gameEngine.gameEnded(room.game)) {
                    room.game.status = "I";
                    room.game.gameStatus = 3;
                    io.to(roomName).emit("gameInterrupted", room.game);
                }
            });
        }
        socket.data.user = undefined;
    });

    //lobby
    socket.on("fetchGames", (callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const games = lobby.getGames();
        if (callback) {
            callback(games);
        }
    });

    socket.on("addGame", (board, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const game = lobby.addGame(socket.data.user, board, socket.id);

        io.to("lobby").emit("lobbyChanged", lobby.getGames());
        if (callback) {
            callback(game);
        }
    });

    socket.on("joinGame", (data, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const game = lobby.getGame(data.id);

        if (socket.data.user.id == game.player1.id) {
            if (callback) {
                callback({
                    errorCode: 400,
                    errorMessage: "User cannot join a game that they created!",
                });
            }
            return;
        }

        game.player2 = socket.data.user;
        game.player2SocketId = socket.id;

        lobby.removeGame(data.id);
        io.to("lobby").emit("lobbyChanged", lobby.getGames());
        if (callback) {
            callback(game);
        }
    });

    socket.on("removeGame", (id, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const game = lobby.getGame(id);
        if (socket.data.user.id != game.player1.id) {
            if (callback) {
                callback({
                    errorCode: 400,
                    errorMessage:
                        "User cannot remove a game that they have not created!",
                });
            }
            return;
        }
        lobby.removeGame(game.id);
        io.to("lobby").emit("lobbyChanged", lobby.getGames());
        if (callback) {
            callback(game);
        }
    });

    socket.on("startGame", (clientGame, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const roomName = "game_" + clientGame.id;
        const game = gameEngine.initGame(clientGame);

        const player1Socket = io.sockets.sockets.get(game.player1SocketId);
        const player2Socket = io.sockets.sockets.get(game.player2SocketId);
        
        player1Socket.join(roomName);
        player2Socket.join(roomName);

        // store the game data directly on the room object:
        const room = socket.adapter.rooms.get(roomName);
        if (!room) {
            socket.adapter.rooms.set(roomName, { game }); // Initialize room game state
        } else {
            room.game = game;
        }
        io.to(roomName).emit("gameStarted", game);
        if (callback) {
            callback(game);
        }
    });

    socket.on("play", (move, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const roomName = "game_" + move.gameId;
        
        const game = socket.adapter.rooms.get(roomName).game;
        const playResult = gameEngine.play(
            game,
            move.index,
            socket.id,
            roomName,
            io
        );
        
        if (playResult !== true) {
            if (callback) {
                callback(playResult);
            }
            return;
        }
    });

    socket.on("quitGame", (gameId, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const roomName = "game_" + gameId;
        // load game state from the game data stored directly on the room object:
        const game = socket.adapter.rooms.get(roomName).game;
        const quitResult = gameEngine.quit(game, socket.id);
        if (quitResult !== true) {
            if (callback) {
                callback(quitResult);
            }
            return;
        }
        socket.leave(roomName);
        io.to(roomName).emit("gameQuitted", {
            userQuit: socket.data.user,
            game: game,
        });
        if (callback) {
            callback(game);
        }
    });

    socket.on("closeGame", (gameId, callback) => {
        if (!util.checkAuthenticatedUser(socket, callback)) {
            return;
        }
        const roomName = "game_" + gameId;
    
        // load game state from the game data stored directly on the room object:
        const game = socket.adapter.rooms.get(roomName).game;
        const closeResult = gameEngine.close(game, socket.id);
        if (closeResult !== true) {
            if (callback) {
                callback(closeResult);
            }
            return;
        }
        socket.leave(roomName);
        if (callback) {
            callback(true);
        }
    });

    //on disconnect    
    socket.on("disconnecting", (reason) => {
        socket.rooms.forEach((room) => {
            if (room == "lobby") {
                lobby.leaveLobby(socket.id);
                io.to("lobby").emit("lobbyChanged", lobby.getGames());
            }
        });
    
        util.getRoomGamesPlaying(socket).forEach(([roomName, room]) => {
            socket.leave(roomName);
            if (!gameEngine.gameEnded(room.game)) {
                room.game.status = "I";
                room.game.gameStatus = 3;
                io.to(roomName).emit("gameInterrupted", room.game);
            }
        });
    });
});
