
angular
    .module('service.shop', [])
    .factory('shopService', [
        '$http',
        function ($http) {

            var url = '/v1/';

            return {

                getCtegoriesShops: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_ctegories_shops',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                getShopsByCategory: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_shops_by_category',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                removeShop: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }

            };
        }]);
