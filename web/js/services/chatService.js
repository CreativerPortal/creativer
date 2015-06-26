angular.module('service.chat', ['service.socket'])
    .factory('chat', ['$rootScope', 'socket', function ($rootScope,socket) {

        var init = function(){
            socket.on("new message", function(data) {
                $rootScope.new_messages = data;
            });
        }

        return {
            init: init
        }

    }]);