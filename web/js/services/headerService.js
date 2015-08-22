
angular
    .module('service.header', [])
    .factory('headerService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                searchProducts: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'search_products',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                searchServices: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'search_services',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                searchPeople: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'search_people',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
