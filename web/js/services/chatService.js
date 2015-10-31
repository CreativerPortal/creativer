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
            $rootScope.ids = [$stateParams.id_user_chat, $rootScope.id_user];
            $rootScope.ids = $rootScope.ids.sort();
            if(data.reviewed == false && ($stateParams.id_user_chat == data.sender || $stateParams.id_user_chat == data.receiver)){
                $rootScope.messages_history.unshift({sender: data.sender, text: data.text, date: data.date, username: data.username, lastname: data.lastname, other_user: data.id, avatar: data.avatar, color: data.color});

                if($rootScope.id_user == data.sender){
                    $rootScope.text_message = null;
                }else{
                    socket.emit('reviewed', {ids: $rootScope.ids, id_user: $rootScope.id_user});
                }
            }else{
                soundClick();
                $rootScope.new_messages.unshift(data);
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