var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mongo = require('mongodb').MongoClient;
var mysql = require('mysql');
var fs = require('fs');

db_connect = "mongodb://127.0.0.1:27017/local";

console.log("start script");

var connection = mysql.createConnection({
    host:     'localhost',
    user:     'slaq',
    password: 'slaku777',
    database: 'creativer'
});

process.on('uncaughtException', function (processError) {
    var stream = fs.createWriteStream("errors.txt");
    stream.once('open', function(fd) {
        stream.write(processError.stack);
        stream.end();
    });
    //console.log(processError.stack);
});

app.get('/', function(req, res){
    res.sendfile('index.html');
});

var sockets = [];

io.on('connection', function(socket){

    if(sockets[socket.handshake.query.id_user] != undefined){
        sockets[socket.handshake.query.id_user].push(socket);

    }else{
        sockets[socket.handshake.query.id_user] = [];
        sockets[socket.handshake.query.id_user].push(socket);
    }


    socket.on('all messages', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_user = data.id_user;
            collection.aggregate([ {$match: {id_users: {$in: [id_user]}}}, {$group: {_id: { id_users: "$id_users" }, text: { $last: "$text" }, sender: { $last: "$sender" }, receiver: { $last: "$receiver" }, reviewed: { $last: "$reviewed" }, date: { $last: "$date" }}} ], function (err, result) {
                if (err) {
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
                    //connection.connect();
                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                        connection.query(queryText, companion, function(err, rows) {
                            for(var row_key in result){
                                for(var r_key in  rows){
                                    if(result[row_key].other_user == rows[r_key].id){
                                        result[row_key].username = rows[r_key].username;
                                        result[row_key].lastname = rows[r_key].lastname;
                                        result[row_key].avatar = rows[r_key].avatar;
                                    }
                                }
                            }
                            socket.emit('all messages', result);
                            //connection.end();
                            db.close();
                        }
                    );
                }
            });
        });
    })


    socket.on('history', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_users = data.ids.sort();
            collection.find({id_users:id_users}).sort({_id: -1}).limit(10).toArray(function (err, result) {
                if (err) {
                } else if (result.length) {
                    var users = [];
                    users.push(id_users[0]);
                    users.push(id_users[1]);
                    //connection.connect();
                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar FROM app_users AS u WHERE u.id IN ("+ users.join(',') +")";
                    connection.query(queryText, users, function(err, rows) {
                        for(var key in rows){
                            if(rows[key].id != data.id_user){
                                var comp = rows[key];
                            }
                        }
                        var res = {
                            messages: result,
                            companion: comp
                        };
                        socket.emit('history', res);
                        //connection.end();
                        db.close();
                        }
                    );
                } else {
                }
            });
        });
    });


    socket.on('disconnect', function(){
        for(key in sockets[socket.handshake.query.id_user]){
            if(sockets[socket.handshake.query.id_user][key].id == socket.id){
                sockets[socket.handshake.query.id_user].slice(key,1)
            };
        }
    });

    socket.on('message', function (data) {
        mongo.connect(db_connect, function (err, db) {
            if (data.ids[0] != "null" && data.ids[1] != "null" && data.ids[0] != data.ids[1] && data.sender != "null") {
                data.ids[0] = parseInt(data.ids[0]);
                data.ids[1] = parseInt(data.ids[1]);
                data.sender = parseInt(data.sender);
                var collection = db.collection('messages');
                if (data.ids[0] != data.sender) {
                    var receiver = data.ids[0];
                } else {
                    var receiver = data.ids[1];
                }
                data.date = new Date();
                collection.insert({
                    id_users: data.ids,
                    sender: data.sender,
                    receiver: receiver,
                    text: data.text,
                    reviewed: false,
                    date: data.date
                }, function (err, result) {
                    if (err) {
                    }
                    else {
                        //connection.connect();
                        var queryText = "SELECT u.id, u.username, u.lastname, u.avatar FROM app_users AS u WHERE u.id IN ("+ data.sender +")";
                        connection.query(queryText, data.sender, function(err, rows) {
                            result.ops[0].other_user = rows[0].id;
                            result.ops[0].username = rows[0].username;
                            result.ops[0].lastname = rows[0].lastname;
                            result.ops[0].avatar = rows[0].avatar;
                            for (var key in data.ids) {
                                var id = data.ids[key];
                                for (var k in sockets[id]) {
                                    sockets[id][k].emit('message', result.ops);
                                }
                            }
                            //connection.end();
                            db.close();
                            }
                        );
                    }
                });
            }
        });

    });

    socket.on('reviewed', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            if(data.ids){
                var id_users = data.ids.sort();
            }
            var id_recipient = data.id_user

            collection.update({id_users: id_users, receiver: id_recipient},{ $set: {reviewed: true}}, { multi: true }, function(err, result) {
                if (err){
                }else{
                    var id = parseInt(socket.handshake.query.id_user);
                    collection.find({ receiver: id_recipient, reviewed: false}).toArray(function (err, result) {
                        socket.emit('new message', result);
                        db.close();
                    })
                }
            });
        });

    });

    socket.on('new message', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_recipient = data.id_user
            collection.find({ receiver: id_recipient, reviewed: false}).sort({_id: -1}).toArray(function (err, result) {
                if (err) {
                    //console.log(err);
                } else {
                    var companion = [];
                    for(var key in result){
                        if(result[key].id_users[0] != data.id_user){
                            companion.push(result[key].id_users[0]);
                            result[key].other_user = result[key].id_users[0];
                        }else{
                            companion.push(result[key].id_users[1]);
                            result[key].other_user = result[key].id_users[1];
                        }
                    }
                    //connection.connect();
                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                    connection.query(queryText, companion, function(err, rows) {
                        for(var row_key in result){
                            for(var r_key in  rows){
                                if(result[row_key].other_user == rows[r_key].id){
                                    result[row_key].username = rows[r_key].username;
                                    result[row_key].lastname = rows[r_key].lastname;
                                    result[row_key].avatar = rows[r_key].avatar;
                                }
                            }
                        }
                        socket.emit('new message', result);
                        //connection.end();
                        db.close();
                        }
                    );
                }
            })
        });
    })


        mongo.connect(db_connect, function (err, db) {
        var collection = db.collection('messages');
        var id = parseInt(socket.handshake.query.id_user);
        collection.find({ receiver: id, reviewed: false}).sort({_id: -1}).toArray(function (err, result) {
            socket.emit('new message', result);
            db.close();
        })
        })

});

http.listen(3000);




