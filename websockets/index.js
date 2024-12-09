import { createUtil } from './util' 

const util = createUtil()

const userTokens = new Map()

const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true,
    },
});

const PORT = process.env.APP_PORT || 8086;

httpServer.listen(PORT, () => {
    console.log(`listening on localhost:${PORT}`);
});

io.on("connection", (socket) => {
    console.log(`client ${socket.id} has connected`);

    socket.on("echo", (message) => {
        socket.emit("echo", message);
    });

    socket.on('register-token', (clientMessage, callback) => {
        const destinationRoomName = 'user_' + clientMessageObj?.destinationUser?.id
        // Check if the destination user is online
        if (io.sockets.adapter.rooms.get(destinationRoomName)) {
            const payload = {
                user: socket.data.user,
                message: clientMessageObj.message,
            }
            // send the "privateMessage" to the destination user (using "his" room)
            io.to(destinationRoomName).emit('privateMessage', payload)
            if (callback) {
                callback({success: true})
            }
        } else {
            if (callback) {
                callback({
                    errorCode: 1,
                    errorMessage:
                    `User "${clientMessageObj?.destinationUser?.name}" is not online!`
                })
            }
        }
    });

    socket.on('send-notification', (data) => {
        console.log('Notification received:', data);

        io.emit('receive-notification', data);
    });

    socket.on('disconnect', () => {
        console.log('A user disconnected:', socket.id);
    });
});

