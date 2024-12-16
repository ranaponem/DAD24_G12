import { createUtil } from './util' 

const PORT = process.env.APP_PORT || 8086;

const util = createUtil()

const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true,
    },
});


httpServer.listen(PORT, () => {
    console.log(`listening on localhost:${PORT}`);
});

io.on("connection", (socket) => {
    console.log(`client ${socket.id} has connected`);

    socket.on('login', (user) => {
        // Stores user information on the socket as "user" property
        socket.data.user = user
        if (user && user.id) {
            socket.join('notif_user_' + user.id)
        }
    })

    socket.on('logout', (user) => {
        if (user && user.id) {
            socket.leave('notif_user_' + user.id)
        }
        socket.data.user = undefined
    })

    socket.on('send-taes-notification', (notificationMessage) => {
        const destinationRoomName = 'notif_user_' + notificationMessage?.user_id
        console.log('notification recceived')
        if (io.sockets.adapter.rooms.get(destinationRoomName)) {
            const payload = {
                title: notificationMessage.title,
                message: notificationMessage.message,
            }
            // send the notification to the destination user (using "his" room)
            io.to(destinationRoomName).emit('taesNotification', payload)
        }
    });
});

