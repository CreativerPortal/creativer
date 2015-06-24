var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mongo = require('mongodb').MongoClient;

db_connect = "mongodb://127.0.0.1:27017/local";

app.get('/', function(req, res){
    res.sendfile('index.html');
});

var sockets = [];

io.on('connection', function(socket){

    console.log(socket.id);

    if(sockets[socket.handshake.query.id_user] != undefined){
        sockets[socket.handshake.query.id_user].push(socket);
        console.log('user connected 2');

    }else{
        console.log('user connected 1');
        sockets[socket.handshake.query.id_user] = [];
        sockets[socket.handshake.query.id_user].push(socket);
    }


    socket.on('history', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_users = data.ids.sort();
            var cursor = collection.find({id_users:id_users});


            cursor.count(function(error,docs){
                if(docs > 0){
                    cursor.each(function (err, doc) {
                        if (err) {
                            console.log(err);
                        } else {
                            //console.log(doc);
                            //doc.messages.slice(0,10);
                            socket.emit('history', doc);
                        }
                    });
                }else{
                    collection.insert({id_users: data.ids, messages:[]}, function(error, docs){
                        socket.emit('history', docs);
                    });
                }

            });
        });
    })


    socket.on('disconnect', function(){
        console.log('user disconnected');
        sockets[socket.handshake.query.id_user] = undefined;
    });

    socket.on('message', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');

            collection.update({ id_users: data.ids }, { $push: { messages: { $each: [{id_user: data.id_user, text: data.text}], $position: 0 } } }, function (err, o) {
                if (err) { console.warn(err.message); }
                else { console.log("chat message inserted into db: " + data); }
            });
        });

        for(var key in data.ids){
            var id = data.ids[key];
            for(var k in sockets[id]){
                sockets[id][k].emit('message', data);
            }
        }
    });

});

http.listen(3000, "192.168.56.101");




