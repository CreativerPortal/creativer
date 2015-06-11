
angular
    .module('service.baraholka', [])
    .factory('baraholkaService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getDataBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_data_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                createPostBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: 'create_post_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
