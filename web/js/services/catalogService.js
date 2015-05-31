
angular
    .module('service.catalog', [])
    .factory('catalogService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getServices: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_services',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getProducts: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_products',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getCatalogAlbums: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_catatalog_albums',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
