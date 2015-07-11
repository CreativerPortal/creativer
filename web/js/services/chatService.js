angular.module('service.chat', ['service.socket'])
    .factory('chat', ['$rootScope', 'socket', '$routeParams', '$timeout', function ($rootScope,socket,$routeParams,$timeout) {

        if(!$rootScope.messages){
            $rootScope.messages = [];
        }

        function soundClick() {
            var audio = new Audio(); // ??????? ????? ??????? Audio
            audio.src = '/sound/drop.wav'; // ????????? ???? ? ????? "?????"
            audio.autoplay = true; // ????????????? ?????????
        }

        socket.on('message', function(data){
            var data = data[0];
            $rootScope.ids = [$routeParams.id_user_chat, $rootScope.id_user];
            $rootScope.ids = $rootScope.ids.sort();
            if(data.reviewed == false && ($routeParams.id_user_chat == data.sender || $routeParams.id_user_chat == data.receiver)){
                $rootScope.messages.unshift({sender: data.sender, text: data.text, date: data.date});


                if($rootScope.id_user == data.sender){
                    $rootScope.text_message = null;
                }else{
                    console.log($rootScope.ids);
                    socket.emit('reviewed', {ids: $rootScope.ids, id_user: $rootScope.id_user});
                }
            }else{
                soundClick();
                $rootScope.new_messages.push(data);
            }
        });

        var init = function(){
            socket.on("new message", function(data) {
                $rootScope.new_messages = data;
            });
        }

        return {
            init: init
        }

    }]);