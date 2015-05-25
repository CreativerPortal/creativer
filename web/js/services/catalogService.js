
angular
    .module('service.catalog', [])
    .factory('catalogService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getCtegories: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_categories',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
