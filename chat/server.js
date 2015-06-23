var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mongo = require('mongodb').MongoClient;

db_connect = "mongodb://127.0.0.1:27017/local";

app.get('/', function(req, res){
    res.sendfile('index.html');
});

io.on('connection', function(socket){
    console.log('user connected');


    socket.on('history', function (msg) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages')
            var stream = collection.find().sort({ _id : -1 }).limit(10).stream();
            stream.on('data', function (chat) {
                socket.emit('history', chat);
            });
        });
    })



        //socket.on('chat message', function(msg){
    //    console.log('message: ' + msg);
    //    io.emit('chat message', msg);
    //});

    socket.on('disconnect', function(){
        console.log('user disconnected');
    });

    socket.on('message', function (data) {
        console.log(data);
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            collection.insert({ content: msg }, function (err, o) {
                if (err) { console.warn(err.message); }
                else { console.log("chat message inserted into db: " + msg); }
            });
        });

        socket.broadcast.emit('chat', data);
    });

});

http.listen(3000, "192.168.56.101");




