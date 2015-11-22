angular.module('service.chat', ['service.socket'])
    .factory('chat', ['$state', '$rootScope', 'socket', '$stateParams', '$timeout', function ($state,$rootScope,socket,$stateParams,$timeout) {

        if(!$rootScope.messages_history){
            $rootScope.messages_history = [];
        }

        function soundClick() {
            var audio = new Audio();
            audio.src = '/sound/drop.wav';
            audio.autoplay = true;
        }

        socket.on('message', function(data){
            $rootScope.message_button = true;
            var data = data[0];
            var id_user_chat = parseInt($stateParams.id_user_chat);
            var id_user = parseInt($rootScope.id_user);
            $rootScope.ids = [id_user_chat, id_user];
            $rootScope.ids = $rootScope.ids.sort();
            $rootScope.id_user = parseInt($rootScope.id_user);
            if(data.reviewed == false && ($stateParams.id_user_chat == data.sender || $stateParams.id_user_chat == data.receiver)){
                $rootScope.messages_history.unshift({sender: data.sender, text: data.text, date: data.date, username: data.username, lastname: data.lastname, other_user: data.id, avatar: data.avatar, color: data.color});

                if($rootScope.id_user == data.sender){
                    $rootScope.text_message = null;
                }else{
                    socket.emit('reviewed', {ids: $rootScope.ids, id_user: $rootScope.id_user});
                }
            }
            if($state.current.name != 'chat' || !$rootScope.focus || ($stateParams.id_user_chat != data.sender && $rootScope.id_user != data.sender || $stateParams.id_user_chat != data.receiver && $rootScope.id_user != data.receiver)){
                soundClick();
                $rootScope.new_messages.unshift(data);
            }

            $rootScope.pause = false;
            $rootScope.writing = false;
            $rootScope.$apply();
        });

        socket.on('old messages', function(data){
            $rootScope.ids = [$stateParams.id_user_chat, $rootScope.id_user];
            $rootScope.ids = $rootScope.ids.sort();
            $rootScope.messages_history = $rootScope.messages_history.concat(data.messages);
            $rootScope.loader_message = false;
        });

        socket.on('end old messages', function(data){
            $rootScope.loader_message = false;
        });

        $rootScope.pause = false;

        $rootScope.$watch("text_message", function () {
            if($rootScope.text_message && $rootScope.text_message.length > 0 && !$rootScope.pause){
                $rootScope.pause = true;
                $rootScope.ids = [$stateParams.id_user_chat, $rootScope.id_user];
                $rootScope.ids = $rootScope.ids.sort();
                socket.emit('writing', {ids: $rootScope.ids, id_user: $rootScope.id_user});
                setTimeout(function(){
                    $rootScope.pause = false;
                }, 5000);
            }
        });

        socket.on('writing', function(data){
            if(data.ids[0] == $stateParams.id_user_chat && data.ids[1] == $rootScope.id_user || data.ids[0] == $rootScope.id_user && data.ids[1] == $stateParams.id_user_chat){
                $rootScope.writing = true;
                setTimeout(function(){
                    $rootScope.writing = false;
                    $rootScope.$apply();
                },5000);
            }
        });

        var init = function(){
            socket.on("new message", function(data) {
                //if($state.current.name != 'chat'){}
                $rootScope.new_messages = data;
            });
        }

        return {
            init: init
        }

    }]);