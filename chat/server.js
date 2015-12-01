var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mongo = require('mongodb').MongoClient;
var ObjectID = require('mongodb').ObjectID;
var mysql = require('mysql');
var fs = require('fs');

db_connect = "mongodb://127.0.0.1:27017/creativer";

console.log("start script");

var stream = fs.createWriteStream("errors.txt");

process.on('uncaughtException', function (processError) {
    stream.once('open', function(fd) {
        stream.write(processError.stack);
        stream.end();
    });
    console.log(processError.stack);
});

app.get('/', function(req, res){
    res.sendfile('index.html');
});

var sockets = [];

io.on('connection', function(socket){

    if(!connection){
        var connection = mysql.createConnection({
            host:     'localhost',
            user:     'slaq',
            password: 'slaku777',
            database: 'creativer'
        });
    }

    var date = new Date("0000");
    var queryText = "UPDATE app_users SET connection_status='online' WHERE id="+socket.handshake.query.id_user+";";
    connection.query(queryText, function(err, rows) {
            console.log("good")
        }
    );

    socket.on('disconnect', function(){
        var date = new Date();
        var queryText = "UPDATE app_users SET connection_status=NOW() WHERE id="+socket.handshake.query.id_user+";";
        connection.query(queryText, function(err, rows) {
                console.log("disconnect")
            }
        );

        for(key in sockets[socket.handshake.query.id_user]){
            if(sockets[socket.handshake.query.id_user][key].id == socket.id){
                sockets[socket.handshake.query.id_user].slice(key,1)
            };
        }

        connection.destroy();
    });


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

                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                        connection.query(queryText, companion, function(err, rows) {
                            for(var row_key in result){
                                for(var r_key in  rows){
                                    if(result[row_key].other_user == rows[r_key].id){
                                        result[row_key].username = rows[r_key].username;
                                        result[row_key].lastname = rows[r_key].lastname;
                                        result[row_key].avatar = rows[r_key].avatar;
                                        result[row_key].color = rows[r_key].color;
                                        result[row_key].connection_status = rows[r_key].connection_status;
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

                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ users.join(',') +")";
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


    socket.on('near messages', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_users = data.ids.sort();

            var id_message = ObjectID(data.id_message);

            collection.find({id_users:id_users, _id: { $gt: id_message}}).sort({_id: 1}).limit(4).toArray(function (err, result1) {
                collection.find({id_users:id_users, _id: { $lte: id_message}}).sort({_id: -1}).limit(5).toArray(function (err, result2) {
                    result1.reverse();
                    var result = result1.concat(result2);
                    if (err) {
                        //console.log(err);
                    } else {
                        var companion = [];
                        for(var key in result){
                            if(result[key].id_users[0] != data.id_user){
                                var id_users_0 = parseInt(result[key].id_users[0]);
                                companion.push(id_users_0);
                                result[key].other_user = id_users_0;
                            }else{
                                var id_users_1 = parseInt(result[key].id_users[1]);
                                companion.push(id_users_1);
                                result[key].other_user = id_users_1;
                            }
                        }
                        var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                        connection.query(queryText, companion, function(err, rows) {
                                for(var row_key in result){
                                    for(var r_key in  rows){
                                        if(result[row_key].other_user == rows[r_key].id){
                                            result[row_key].username = rows[r_key].username;
                                            result[row_key].lastname = rows[r_key].lastname;
                                            result[row_key].avatar = rows[r_key].avatar;
                                            result[row_key].color = rows[r_key].color;
                                            result[row_key].connection_status = rows[r_key].connection_status;
                                        }
                                    }
                                }
                                socket.emit('near messages', result)
                                db.close();
                            }
                        );
                    }

                });
            });
        });
    });


    socket.on('old messages', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_users = data.ids.sort();
            var length = data.length;
            collection.find({id_users:id_users}).sort({_id: -1}).skip(length).limit(10).toArray(function (err, result) {
                if (err) {
                } else if (result.length) {
                    var users = [];
                    users.push(id_users[0]);
                    users.push(id_users[1]);
                    connection.connect(function(err){
                    });
                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ users.join(',') +")";
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

                            socket.emit('old messages', res);
                            //connection.end();
                            db.close();
                        }
                    );
                } else {
                }
                if(result.length == 0){
                    socket.emit('end old messages', err);
                }
            });
        });
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
                        var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ data.sender +")";
                        connection.query(queryText, data.sender, function(err, rows) {
                            result.ops[0].other_user = rows[0].id;
                            result.ops[0].username = rows[0].username;
                            result.ops[0].lastname = rows[0].lastname;
                            result.ops[0].avatar = rows[0].avatar;
                            result.ops[0].color = rows[0].color;
                            result.ops[0].connection_status = rows[0].connection_status;
                            for (var key in data.ids) {
                                var id = data.ids[key];
                                for (var k in sockets[id]) {
                                    sockets[id][k].emit('message', result.ops);
                                }
                            }
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
                        if (err) {
                            //console.log(err);
                        } else {
                            var companion = [];
                            for(var key in result){
                                if(result[key].id_users[0] != data.id_user){
                                    var id_users_0 = parseInt(result[key].id_users[0]);
                                    companion.push(id_users_0);
                                    result[key].other_user = id_users_0;
                                }else{
                                    var id_users_1 = parseInt(result[key].id_users[1]);
                                    companion.push(id_users_1);
                                    result[key].other_user = id_users_1;
                                }
                            }
                            var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                            connection.query(queryText, companion, function(err, rows) {
                                    for(var row_key in result){
                                        for(var r_key in  rows){
                                            if(result[row_key].other_user == rows[r_key].id){
                                                result[row_key].username = rows[r_key].username;
                                                result[row_key].lastname = rows[r_key].lastname;
                                                result[row_key].avatar = rows[r_key].avatar;
                                                result[row_key].color = rows[r_key].color;
                                                result[row_key].connection_status = rows[r_key].connection_status;
                                            }
                                        }
                                    }
                                    socket.emit('new message', result);
                                    db.close();
                                }
                            );
                        }
                    })
                }
            });
        });

    });

    socket.on('new message', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_recipient = parseInt(data.id_user);
            collection.find({ receiver: id_recipient, reviewed: false}).sort({_id: -1}).toArray(function (err, result) {
                if (err) {
                    //console.log(err);
                } else {
                    var companion = [];
                    for(var key in result){
                        if(result[key].id_users[0] != data.id_user){
                            var id_users_0 = parseInt(result[key].id_users[0]);
                            companion.push(id_users_0);
                            result[key].other_user = id_users_0;
                        }else{
                            var id_users_1 = parseInt(result[key].id_users[1]);
                            companion.push(id_users_1);
                            result[key].other_user = id_users_1;
                        }
                    }
                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                    connection.query(queryText, companion, function(err, rows) {
                        for(var row_key in result){
                            for(var r_key in  rows){
                                if(result[row_key].other_user == rows[r_key].id){
                                    result[row_key].username = rows[r_key].username;
                                    result[row_key].lastname = rows[r_key].lastname;
                                    result[row_key].avatar = rows[r_key].avatar;
                                    result[row_key].color = rows[r_key].color;
                                    result[row_key].connection_status = rows[r_key].connection_status;
                                }
                            }
                        }
                        socket.emit('new message', result);
                            db.close();
                        }
                    );
                }
            })
        });
    })

    socket.on('search by reports', function (data) {
        mongo.connect(db_connect, function (err, db) {
            var collection = db.collection('messages');
            var id_user = parseInt(data.id_user);
            var search_text = data.search_text;
            collection.find({ $or : [{"receiver": id_user, 'text': {'$regex': search_text}}, {"sender": id_user, 'text': {'$regex': search_text}}] }).sort({_id: -1}).limit(50).toArray(function (err, result) {
                if (err) {
                    //console.log(err);
                } else {
                    var companion = [];
                    for(var key in result){
                        if(result[key].id_users[0] != data.id_user){
                            var id_users_0 = parseInt(result[key].id_users[0]);
                            companion.push(id_users_0);
                            result[key].other_user = id_users_0;
                        }else{
                            var id_users_1 = parseInt(result[key].id_users[1]);
                            companion.push(id_users_1);
                            result[key].other_user = id_users_1;
                        }
                    }
                    var queryText = "SELECT u.id, u.username, u.lastname, u.avatar, u.color, u.connection_status FROM app_users AS u WHERE u.id IN ("+ companion.join(',') +")";
                    connection.query(queryText, companion, function(err, rows) {
                            for(var row_key in result){
                                for(var r_key in  rows){
                                    if(result[row_key].other_user == rows[r_key].id){
                                        result[row_key].username = rows[r_key].username;
                                        result[row_key].lastname = rows[r_key].lastname;
                                        result[row_key].avatar = rows[r_key].avatar;
                                        result[row_key].color = rows[r_key].color;
                                        result[row_key].connection_status = rows[r_key].connection_status;
                                    }
                                }
                            }
                            socket.emit('search by reports', result);
                            db.close();
                        }
                    );
                }
            })
        });
    })

    socket.on('writing', function (data) {
        for (var key in data.ids) {
            var id = data.ids[key];
            for (var k in sockets[id]) {
                if(id !=  data.id_user){
                    sockets[id][k].emit('writing', data);
                }
            }
        }
    });

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




