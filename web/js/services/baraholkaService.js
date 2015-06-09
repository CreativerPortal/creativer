
angular
    .module('service.baraholka', [])
    .factory('baraholkaService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getCategoriesBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_categories_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
