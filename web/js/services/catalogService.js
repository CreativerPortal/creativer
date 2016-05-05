
angular
    .module('service.catalog', [])
    .factory('catalogService', [
        '$http',
        function ($http) {

            var url = '/v1/';

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
                getCatalogServiceAlbums: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_catalog_service_albums',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getCatalogProductAlbums: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_catalog_product_albums',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
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
                getNewsEvents: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_news_events',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getLikesByImagesId: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_likes_by_images_id',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
