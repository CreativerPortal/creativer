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


            collection.find({id_users:id_users}).sort({_id: -1}).limit(10).toArray(function (err, result) {
                if (err) {
                    console.log(err);
                } else if (result.length) {
                    socket.emit('history', result);
                } else {
                    console.log('No document(s) found with defined "find" criteria!');
                }
                //Close connection
                db.close();
            });

        });
    })


    socket.on('disconnect', function(){
        console.log('user disconnected');
        for(key in sockets[socket.handshake.query.id_user]){
            if(sockets[socket.handshake.query.id_user][key].id == socket.id){
                sockets[socket.handshake.query.id_user].slice(key,1)
            };
        }
    });

    socket.on('message', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            for(key in data.ids){
                if(data.ids[0] != data.id_user){
                    var id_recipient = data.ids[0];
                }else{
                    var id_recipient = data.ids[1];
                }
            }
            data.date = new Date();
            collection.insert({ id_users: data.ids, id_user: data.id_user, id_recipient:id_recipient, text: data.text, reviewed: false, date: data.date }, function (err, o) {
                if (err) { console.warn(err.message); }
                else { console.log("chat message inserted into db: " + data); }
            });

            for(var key in data.ids){
                var id = data.ids[key];
                for(var k in sockets[id]){
                    sockets[id][k].emit('message', data);
                }
            }

        });

    });


    socket.on('reviewed', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            console.log('good');
            collection.update({ id_users: data.ids, messages: {$elemMatch: {reviewed: false}} }, { $set: { "messages.$.reviewed": true } }, false, true);

        });

    });

    mongo.connect(db_connect, function (err, db) {
        var collection = db.collection('messages');
        var id = parseInt(socket.handshake.query.id_user);
        collection.find({ id_users: {$in: [id]}, reviewed: false}).toArray(function (err, result) {
            socket.emit('new message', result);
        })
    })

});

http.listen(3000, "192.168.56.101");




