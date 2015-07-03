var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mongo = require('mongodb').MongoClient;
var mysql = require('mysql');

db_connect = "mongodb://127.0.0.1:27017/local";

var connection = mysql.createConnection({
    host:     'localhost',
    user:     'slaq',
    password: 'slaku777',
    database: 'creativer'
});

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


    socket.on('all messages', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_user = data.id_user;
            collection.aggregate([ {$match: {id_users: {$in: [id_user]}}}, {$group: {_id: { id_users: "$id_users" }, text: { $last: "$text" }, sender: { $last: "$sender" }, reviewed: { $last: "$reviewed" }, date: { $last: "$date" }}} ], function (err, result) {
                if (err) {
                    console.log(err);
                } else {
                    var companion = [];
                    for(var key in result){
                        if(result[key]._id.id_users[0] != data.id_user){
                            companion.push(result[key]._id.id_users[0]);
                            result[key].other_user = result[key]._id.id_users[0];
                        }else{
                            companion.push(result[key]._id.id_users[1]);
                            result[key].other_user = result[key]._id.id_users[1];
                        }
                    }

                    connection.connect(function(err) {});
                    var queryText = "SELECT u.id, u.username, u.lastname, a.img FROM app_users AS u INNER JOIN avatar AS a ON u.id = a.user_id WHERE u.id IN ("+ companion.join(',') +")";
                        connection.query(queryText, companion, function(err, rows) {
                            for(var row_key in result){
                                for(var r_key in  rows){
                                    if(result[row_key].other_user == rows[r_key].id){
                                        result[row_key].username = rows[r_key].username;
                                        result[row_key].lastname = rows[r_key].lastname;
                                        result[row_key].img = rows[r_key].img;
                                    }
                                }
                            }
                            socket.emit('all messages', result);
                        }
                    );

                }
                //Close connection
                db.close();
            });

        });
    })


    socket.on('history', function (data) {
        console.log('history');
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
            if(data.ids[0] != data.sender){
                var receiver = data.ids[0];
            }else{
                var receiver = data.ids[1];
            }
            data.date = new Date();
            collection.insert({ id_users: data.ids, sender: data.sender, receiver: receiver, text: data.text, reviewed: false, date: data.date }, function (err, result) {
                if (err) { console.warn(err.message); }
                else {
                    console.log(result.ops);
                    for(var key in data.ids){
                        var id = data.ids[key];
                        for(var k in sockets[id]){
                            sockets[id][k].emit('message', result.ops);
                        }
                    }

                }
            });

        });

    });


    socket.on('reviewed', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_users = data.ids.sort();
            for(key in data.ids){
                if(data.ids[0] != data.id_user){
                    var id_recipient = data.ids[0];
                }else{
                    var id_recipient = data.ids[1];
                }
            }
            collection.update({id_users: id_users},{ $set: {reviewed: true}}, { multi: true }, function(err, result) {
                if (err){
                    console.log('bad');
                }else{
                    socket.emit('reviewed', {id_user: id_recipient});
                    var id = parseInt(socket.handshake.query.id_user);
                    collection.find({ id_users: {$in: [id]}, reviewed: false}).toArray(function (err, result) {
                        socket.emit('new message', result);
                    })
                }
            });

        });

    });

    mongo.connect(db_connect, function (err, db) {
        var collection = db.collection('messages');
        var id = parseInt(socket.handshake.query.id_user);
        collection.find({ id_users: {$in: [id]}, reviewed: false}).sort({_id: -1}).toArray(function (err, result) {
            socket.emit('new message', result);
        })
    })

});

http.listen(3000, "192.168.56.101");




