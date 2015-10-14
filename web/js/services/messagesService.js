angular.module('service.messages', [])
    .factory('messagesService', [
        '$http',
        function ($http) {

            var url = '/v1/';

            return {
                getUser: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_user',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
