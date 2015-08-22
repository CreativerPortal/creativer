
angular
    .module('service.event', [])
    .factory('eventService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getCity: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_city',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
